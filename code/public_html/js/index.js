requirejs.config({
    baseUrl:"./js",
    paths: {
        "jquery": "./baseLib/jquery-1.11.3"
    }
});
require(["jquery","menu","treelistexd"], function($,menudata,treelistexd) {
    $(function(){

    });
});
