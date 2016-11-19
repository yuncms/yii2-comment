/**
 * 发布评论
 * @param token
 * @param source_type
 * @param source_id
 * @param content
 * @param to_user_id
 */
function add_comment(source_type, source_id, content, to_user_id) {
    var postData = {sourceId: source_id, sourceType: source_type, content: content};
    if (to_user_id > 0) {
        postData.to_user_id = to_user_id;
    }
    jQuery.post('/comment/default/store', postData, function (html) {
        jQuery("#comments-" + source_type + "-" + source_id + " .widget-comment-list").append(html);
        jQuery("#comment-" + source_type + "-content-" + source_id).val('');
    });
}

/**
 * 加载评论
 * @param source_type
 * @param source_id
 */
function load_comments(source_type, source_id) {
    jQuery.get('/comment/default/list',{
        sourceId: source_id, sourceType: source_type
    }, function (html) {
        jQuery("#comments-" + source_type + "-" + source_id + " .widget-comment-list").append(html);
    });
}

/**
 * 清除评论
 * @param source_type
 * @param source_id
 */
function clear_comments(source_type, source_id) {
    jQuery("#comments-" + source_type + "-" + source_id + " .widget-comment-list").empty();
}

/*评论提交*/
jQuery(".comment-btn").click(function(){
    var source_id = jQuery(this).data('source_id');
    var source_type = jQuery(this).data('source_type');
    var to_user_id = jQuery(this).data('to_user_id');
    var content = jQuery("#comment-"+source_type+"-content-"+source_id).val();
    add_comment(source_type,source_id,content,to_user_id);
    jQuery("#comment-content-"+source_id+"").val('');
});
