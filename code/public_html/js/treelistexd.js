define(["jquery"], function($) {
    
    $.fn.Treelist=function(ops){
        return this.each(function(idx,elm){
            //console.log(this,idx,elm);
            var $elm=$(elm);
            var id=$(elm).data("id")||$(elm).attr("data-id"),
            text=$(elm).data("name")||$(elm).attr("data-name"),
            data=$(elm).data("init")||$(elm).attr("data-init");

            var settings=$.extend({},$.fn.Treelist.defaults,{id:id,name:name,data:$.parseJSON(data)}, ops||{});
            this.rs={
                treelist:new treelist($(this),settings)
            };
        });
    }
    $.fn.Treelist.defaults={

    };
    function treelist(elm,ops){
        this.dom=elm;
        this.settings=ops;
        this.init();
    }
    treelist.prototype.init=function(){
        // console.log(this);
        var fd=function (){
            var c="";
            return function(){

            } 
        }
    }
    $(function(){
            $(".treelistexd").Treelist();
    });
});
