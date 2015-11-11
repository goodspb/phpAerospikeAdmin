/**
 * Created by goodspb on 15/11/10.
 */

/**
 * delete confirm function
 * @param _param
 * @param _url
 * @param _remove_id
 */
function confirm_delete( _param , _url , _remove_id ){
    var i = layer.confirm(Lang.delete_question, {
        title: false,
        btn: [Lang.sure_btn,Lang.cancle_btn]
    }, function(){
        $.post(_url,_param,function(data){
            if(data.status){
                $('#'+_remove_id).remove();
                layer.close(i);
            }else{
                layer.msg(data.msg,{icon:5});
            }
        });
    });
}
