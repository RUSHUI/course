requirejs.config({
    baseUrl:"./js",
    paths: {
        "jquery": "./baseLib/jquery-1.11.3"
    }
});
require(["jquery","menu","treelist"], function($,menudata,treelist) {
    $(function(){

    });
});
