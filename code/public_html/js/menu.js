define(["jquery"], function($) {
   $(".nav-list").on("click","li",function(){
           var $this=$(this),_tmp="";
           var cmd=(_tmp=$this.data("cmd"))?_tmp:$this.attr("data-cmd");
           location.href=location.origin+location.pathname+"?"+"page="+cmd;
       });
});
