/**
 * @file riari/laravel-forum-frontend のBladeテンプレートから分離したスクリプト。
 * @copyright 2015 Rick Mann
 * @license MIT
 * @see [laravel-forum-frontend/show.blade.php at 1.1.6 · Riari/laravel-forum-frontend]{@link https://github.com/Riari/laravel-forum-frontend/blob/1.1.6/views/thread/show.blade.php#L100-L103}
 */

$('tr input[type=checkbox]').change(function () {
	var postRow = $(this).closest('tr').prev('tr');
	$(this).is(':checked') ? postRow.addClass('active') : postRow.removeClass('active');
});
