<?php

namespace App\Http\Controllers;

use App\{Dictionary, Revision};
use App\Http\Requests\DiffRevisionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class RevisionsController extends Controller
{
    /**
     * 更新後の辞書の内容を表示します。
     *
     * @param \App\Dictionary $dictionary
     * @param \App\Revision $revision
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function show(Dictionary $dictionary, Revision $revision, Request $request): BaseResponse
    {
        return $revision->dictionary_id === $dictionary->id
            ? response($revision->data)->header('content-type', 'text/csv; header=present; charset=UTF-8')
            : redirect()->route(
                'dictionaries.show',
                ['dictionary' => $revision->dictionary_id, 'revision' => $revision->id],
                Response::HTTP_MOVED_PERMANENTLY
            );
    }
    
    /**
     * 更新後の辞書の内容を表示します。
     *
     * @param \App\Dictionary $dictionary
     * @param \App\Revision $revisionA
     * @param \App\Revision $revisionB
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function diff(Dictionary $dictionary, DiffRevisionRequest $request)
    {
        $revisionIds = $request->input('revisions');
        sort($revisionIds, SORT_NUMERIC);
        $revisions = array_map(function (string $id): Revision {
            return Revision::find($id);
        }, $revisionIds);
        
        foreach ($revisions as $revision) {
            if ($revision->dictionary_id !== $dictionary->id) {
                return redirect()
                    ->route('dictionaries.show', ['dictionary' => $dictionary->id], Response::HTTP_SEE_OTHER)
                    ->with('_status', Response::HTTP_BAD_REQUEST);
            }
        }
        
        $oldTags = $revisions[0]->tags;
        $newTags = $revisions[1]->tags;
        $tags = [
            'added' => array_diff($newTags, $oldTags),
            'deleted' => array_diff($oldTags, $newTags),
        ];
        
        $oldFiles = $revisions[0]->files;
        $newFiles = $revisions[1]->files;
        $files = [
            'added' => array_keys(array_diff_key($newFiles, $oldFiles)),
            'deleted' => array_keys(array_diff_key($oldFiles, $newFiles)),
            'modified' => array_keys(
                array_diff(array_intersect_key($oldFiles, $newFiles), array_intersect_key($newFiles, $oldFiles))
            ),
        ];
        
        return view('revisions.diff')->with([
            'dictionary' => $dictionary,
            'revisions' => $revisions,
            'tags' => array_flatten($tags) ? $tags : null,
            'files' => array_flatten($files) ? $files : null,
        ]);
    }
}
