define(["jquery"], function($) {
    var method=null;
    /**
     * [Treelist 插件名称]
     * @param {[object]} ops [可接收的配置参数]
     * @return {JQ集} [jq查找的对象集]
     */
    $.fn.Treelist=function(ops){
        method=ops.method;
        return this.each(function(idx,elm){
            var _ops={
                pid:$(elm).data("id")||$(elm).attr("data-id"),
                name:$(elm).data("name")||$(elm).attr("data-name")
            };
            var settings=$.extend(true,$.fn.Treelist.defaults,_ops,ops||{});
            this.rs={
                treelist:new treelist($(this),settings)
            };
        });        
    };
    $.fn.Treelist.defaults={
        preWidth:30,
        vedioIcon:true,
        knowledgePointPreWidth:120,
        attachmentPreWidth:150,
        delUrl:"./data/del_course_treelist.json",
        preText:["课程","部分","章","节","知识点","附件"],
        nodeHtmlTpl:{
            "tree":'<div data-params="" class="treelist-item">\
            <div class="field-text">\
                <label>[ins]field-label[ins]</label>\
                <input readonly type="text" placeholder="[ins]text-holder[ins]" class="form-field" value="[ins]field-label[ins]">\
            </div>\
            <div class="tools-operation [ins]isHave-vedio[ins]">\
                <span class="operation">\
                    <i data-cmd="subn" class="subn rs-list3" title="添加内容"></i>\
                    <i data-cmd="nlgpt" class="nlgpt rs-clipboard3 rs-clipboard" title="添加知识点"></i>\
                    <i data-cmd="attachment" class="attachment rs-attachment2" title="添加附件"></i>\
                    <i data-cmd="testlisten" class="testlisten rs-volume4" title="选为试听"></i>\
                    <i data-cmd="write" class="write rs-pencil4" title="编辑内容"></i>\
                </span>\
                <span class="crud" style="margin-left:10px;">\
                    <i data-cmd="save" class="save rs-download" title="保存记录"></i>\
                    <i data-cmd="del" class="del rs-trashcan not-allowed" title="删除记录"></i>\
                    <i data-cmd="state" class="state rs-checkmark2" title="初始状态"></i>\
                </span>\
            </div>\
            <div class="clear-float"></div>\
        </div>',
        "attachment":'',
        "coursepoint":''
        },
        chineseNum:["零","一","二","三","四","五","六","七","八","九","十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十"]
    };
    /**
     * [treelist 插件构造函数]
     * @param  {[$dom]} elm [组件对应的元素的jq对象]
     * @param  {[object]} ops [配置参数]
     * @return {[type]}     [description]
     */
    function treelist(elm,ops){
        this.dom=elm;//组件实例缓存dom元素
        this.treelist=this.init(ops);//实例对象的treelist属性指向自己
        this.tpl={};
    }
    /**
     * [init 组件初始化]
     * @param  {obj} ops [初始化函数接收配置参数，可被重复初始化，不必销毁]
     * @return {obj}     [treelist实例对象]
     */
    treelist.prototype.init=function(ops){
        this.settings=ops;//实例对象缓存配置参数
        this.postData(function(data){
            this.render(data);//render数据方法
            this.regEvent();//注册事件方法
        });
        return this;
    };
    treelist.prototype.preText=function( type,idx ){

        var chineseNum=this.settings.chineseNum;
        
     
        return {
            label:"第"+chineseNum[idx]+ this.settings.preText[type]+"：",
            placeholder:"请输入本"+this.settings.preText[type]+"标题"
        };
    };
    treelist.prototype.postData=function(fn){
        var ths=this;
        $.ajax({
            url:ths.settings.url,
            data:{
                //id:ths.settings.dataid,
                id:"参考课程id"
            },
            success:function(e){
                if(e.code!=0){
                    return console.log("error code:"+ e.code + ", "+e.msg,e);
                }                
                fn&&fn.call(ths,e.data);
            },
            error:function(e){
                console.log("获取数据出错",e);
            }
        });
    };
    treelist.prototype.render=function( data ){

        var ths = this, index = 0, pre_index_text = "rs" ;

        var drawTreeATT = function _ATT( pid, data, selector ,num ){
            
            var _temp = "";

            if( data && data.length > 0 ){
                for (var i = 0, len = data.length; i < len; i++ ) {
                    
                    var _id           = data[i].id,
                        _name         = data[i].name,
                        _weight       = data[i].weight,
                        _attachments_type   = data[i].attachments_type,
                        _course_catalog_id  = data[i].course_catalog_id,
                        _data_path          = data[i].data_path,
                        _selector           = selector + "_" + ( i + 1 ),

                        _create_time        = data[i].create_time;

                    if( num ){
                        _selector = selector + "_" + ( num + 1 );
                        num++;
                    }

                    _temp += '<div ';
                    _temp += ' data-id=' + _id;
                    _temp += ' data-pid=' + pid;
                    _temp += ' data-selector=' + _selector;
                    _temp += ' data-name=' + _name;
                    _temp += ' data-index=' + i;
                    _temp += ' data-weight=' + _weight;
                    _temp += ' data-course_catalog_id=' + _course_catalog_id;
                    _temp += ' data-attachments_type = ' + _attachments_type   ;
                    _temp += ' data-data_path = ' + _data_path                  ;
                    _temp += ' data-create_time = ' + _create_time             ;
                    if( i === 0 ){
                        _temp += ' class="treelist-item first treelist-leaf-att text'; 
                    }else{
                        _temp += ' class="treelist-item treelist-leaf-att text'; 
                    }
                    _temp += '" >';
                    
                    _temp += '<div class="tap">';
                     if( _attachments_type == 2){//评测类
                        _temp += '<i class="rs-link"></i>';
                     }else if( _attachments_type == 1 ){//视频类
                        _temp += '<i class="rs-video-camera"></i>';
                     }else if( _attachments_type == 3 ){//下载类
                        _temp += '<i class="rs-download5"></i>';
                     }
                    
                    _temp += '<span>附件附件附件附件附件附件附件</span>';




                    _temp += '</div>';
                    
                    // _temp += '<div class="field-text"><label>' + "附件 " + '</label>';
                    // _temp += '<input readonly type="text" placeholder=""';
                    // _temp += ' class="form-field" value="' + _name + '"/></div>';
                    // _temp += '<div class="tools-operation">';
                    // _temp += '<span class="operation">';

                    // _temp += '<i data-cmd="zoomin" class="zoomin rs-zoomout" title="编辑完成"></i>';
                    // _temp += '<i data-cmd="testlisten" class="testlisten rs-volume4" title="选为试听"></i>';
                    // _temp += '<i data-cmd="write" class="write rs-pencil4" title="编辑内容"></i>';
                    // _temp += '</span><span class="crud" style="margin-left:10px;">';
                    // _temp += '<i data-cmd="save" class="save rs-download" title="保存记录"></i>';
                    // _temp += '<i data-cmd="del" class="del rs-trashcan" title="删除记录"></i>';
                    // _temp += '<i data-cmd="state" class="state rs-checkmark2" title="初始状态"></i>';
                    

                    // if( _attachments_type == 2){//评测类
                    //     _temp += '<i data-cmd="vedio" class="vedio rs-link" title="视频"></i>';
                    //  }else if( _attachments_type == 1 ){
                    //     _temp += '<i data-cmd="vedio" class="vedio rs-video-camera" title="视频"></i>';
                    //  }else if( _attachments_type == 0 ){
                    //     _temp += '<i data-cmd="vedio" class="vedio rs-download5" title="视频"></i>';
                    //  }
                    // _temp += '</span></div><div class="clear-float"></div>';
                    _temp += '</div>';

                };
            }
            if(!ths.tpl.kpnode){
                ths.tpl.kpnode=_temp;
            }
            return _temp;
        };
        var drawTreeKP = function _KP( pid, data, selector ){
            
            var _temp = "";

            if( data && data.length > 0 ){
                for (var i = 0, len = data.length; i < len; i++ ) {
                    
                    var _id           = data[i].id,
                        _name         = data[i].name,
                        _course_catalog_id = data[i].course_catalog_id,
                        _selector             = selector + "_" + ( i + 1 ),  
                        _weight       = data[i].weight;
                    
                    _temp += '<div ';
                    _temp += ' data-id=' + _id;
                    _temp += ' data-pid=' + pid;
                    _temp += ' data-selector=' + _selector;
                    _temp += ' data-name=' + _name;
                    _temp += ' data-weight=' + _weight;
                    _temp += ' data-course_catalog_id=' + _course_catalog_id;
                    _temp += ' style="margin-left:120px"  class="treelist-item treelist-leaf-kp'; 
                    _temp += '" >';

                    _temp += '<div class="field-text"><label>' + "知识点 " + '</label>';
                    _temp += '<input readonly type="text" placeholder=""';
                    _temp += ' class="form-field" value="' + _name + '"/></div>';
                    _temp += '<div class="tools-operation">';
                    _temp += '<span class="operation">';

                    _temp += '<i data-cmd="edit" class="edit rs-edit" title="编辑内容"></i>';
                    _temp += '<i data-cmd="testlisten" class="testlisten rs-volume4" title="选为试听"></i>';
                    _temp += '<i data-cmd="write" class="write rs-pencil4" title="编辑名称"></i>';
                    _temp += '</span><span class="crud" style="margin-left:10px;">';
                    _temp += '<i data-cmd="save" class="save rs-download" title="保存记录"></i>';
                    _temp += '<i data-cmd="del" class="del rs-trashcan" title="删除记录"></i>';
                    _temp += '<i data-cmd="state" class="state rs-checkmark2" title="初始状态"></i>';
                    
                    _temp += '<i data-cmd="vedio" class="vedio rs-play" title="视频"></i>';
                    // rs-warning2
                    
                    _temp += '</span></div><div class="clear-float"></div></div>';

                };
            }
            if(!ths.tpl.attnode){
                ths.tpl.attnode=_temp;
            }
            return _temp;

        };
        var drawTree = function _drawTree( pid, data, selector ){

            var _temp   = "", _serial = "";
            var _selector       = selector + "_" + 0;
            

            if( data && data.length > 0 ){
                var selectArr=_selector.split("_");
                switch( selectArr.length ){
                    case 2:
                        console.log("课程节点",selectArr);
                    break;
                    case 3:
                        _serial = selector + "_section";
                        console.log("section节点",_serial,selectArr);
                    break;
                    case 4:
                        _serial = selector + "_artcle";
                        console.log("artcle节点",_serial,selectArr);
                    break;
                    case 5:
                        _serial = selector + "_joint";
                        console.log("joint节点",_serial,selectArr);
                    break;
                    default:console.log("分割参数出错！");
                }
                for( var i = 0; i < data.length; i ++ ){

                    var _id           = data[i].id,
                        _name         = data[i].name,
                        _catalog_kp   = data[i].catalog_kp,
                        _catalog_att  = data[i].catalog_att,
                        _weight       = data[i].weight;
                        _selector       = selector + "_" + ( i + 1 );

                    _temp +='<div';                    
                    _temp += ' data-selector=' + _selector          ;
                    _temp += ' data-serial=' + _serial          ;
                    
                    _temp += ' data-id = ' + _id                    ;
                    _temp += ' data-name ="' + _name + '"'          ;
                    _temp += ' data-weight =' + _weight             ;
                   

                   

                    var _status       = data[i].status,
                        _is_audition  = data[i].is_audition,
                        _course_id    = data[i].course_id,
                        _sub_children = data[i].sub_children;

                    
                    _temp += ' data-status = ' + _status           ;
                    _temp += ' data-is_audition = ' + _is_audition ;
                    _temp += ' data-course_id = ' + _course_id     ;
                    _temp += ' data-sub_num = ' + _sub_children.length ;
                    _temp += ' class="treelist-item'               ; 
                    _temp += ' type-tree-limb warning'             ;
                    _temp += '" >';
                    _temp += '<div class="field-text"><label>' + _selector + '</label>';
                    _temp += '<input readonly type="text" placeholder=""'               ;
                    _temp += ' class="form-field" value="' + _name + '"/></div>'        ;
                    _temp += '<div class="tools-operation">'        ;
                    _temp += '<span class="operation">'             ;
                    _temp += '<i data-cmd="subn" class="subn rs-folder-plus" title="添加内容"></i>'; 
                    _temp += '<i data-cmd="nlgpt" class="nlgpt rs-clipboard3 rs-clipboard" title="添加知识点"></i>';
                    _temp += '<i data-cmd="attachment" class="attachment rs-attachment2" title="添加附件"></i>';
                    _temp += '<i data-cmd="testlisten" class="testlisten rs-volume4" title="选为试听"></i>';
                    _temp += '<i data-cmd="write" class="write rs-pencil4" title="编辑内容"></i>';
                    _temp += '</span><span class="crud" style="margin-left:10px;">';
                    _temp += '<i data-cmd="save" class="save rs-download" title="保存记录"></i>';
                    _temp += '<i data-cmd="del" class="del rs-trashcan" title="删除记录"></i>';
                    _temp += '<i data-cmd="state" class="state rs-checkmark2" title="初始状态"></i>';
                    
                    _temp += '<i data-cmd="fold" data-status="opened" class="fold rs-circle-up" title="折叠/展开"></i>'; 
                    // rs-warning2
                    
                    _temp += '</span></div><div class="clear-float"></div></div>';
                    if(!ths.tpl.treenode){
                        ths.tpl.treenode=_temp;
                    }
                    _temp += drawTreeATT( _id, _catalog_att, _selector);
                    _temp += drawTreeKP( _id, _catalog_kp, _selector );
                    
                    _temp += arguments.callee( _id, _sub_children, _selector );              
                }
            }else{
                return _temp;
            }
            return _temp;
        };
        this.dom.html( drawTree( "tree-root", data, pre_index_text ) );

        var type=["section","artcle","joint"];


        for(var i=1;i<=type.length;i++){

            this.dom.find("[data-serial$="+type[i-1]+"]").each(function(idx){

                $(this).css( "margin-left", ths.settings.preWidth * i );
                $(this).find(".field-text label").text( ths.preText( i, idx +1 ).label );

            });
        }
        this.draw={
            "drawTree":drawTree,
            "drawTreeKP":drawTreeKP,
            "drawTreeATT":drawTreeATT
        };
        return this;
    };
    treelist.prototype.remove=function(){
        this.dom.empty();
        return this;
    };

    treelist.prototype.regEvent = function(){

        var ths = this;
        var _ievent = function( cmd, item ){

            var $item = $( item ), $this = $( this );

            //console.log(item);
            
            switch( cmd ){
                case "fold" :
                    var selector = $item.data( "selector" ) || $item.attr( "data-selector" );
                    fold.call(ths,selector,$this);
                break;
                case "subn" :
                    var selector = $item.data( "selector" ) || $item.attr( "data-selector" ),
                        len = $item.data("sub_num") || $item.attr("data-sub_num");
                    subn.call( this, parseInt( len ), selector, $item );
                break;
                case "nlgpt" :

                break;
                
                case "attachment" :
                    ths.attEdit($item.attr("data_id"),$item,{});
                break;

                case "testlisten" :
                    $item.find(">.tools-operation i.testlisten").toggleClass('pass').toggleClass('done');
                break;

                case "write" :
                    if(!$this.hasClass('done')){
                        $item.find(">.field-text input").removeAttr("readonly").focus();
                        $this.addClass('done');
                    }else{
                        alert("请点击对勾完成编辑后数据保存")
                    }
                break;

                case "save":
                    
                break;

                case "del":
                var confirm=window.confirm("确定删除当前章节和其所包含的内容吗？");
                if(confirm){
                    var id=$item.attr("data-id"),delUrl=ths.settings.delUrl;
                    ths.deleteItem.call(item,id,delUrl,function(data){
                        $item.remove();
                    }); 
                }                                   
                break;
                default:console.log("事件未注册在指定元素上,请检查");
            }
        };
        var fold = function( selector, $this ){
            if( $this.attr('data-status') === "opened" ){
                ths.dom.find( "[data-selector^=" + selector + "_]" ).each(function(idx){
                    console.log(idx);
                    (function(ths,idx){
                        setTimeout(function(){
                            $(ths).hide();
                        },10*idx);
                    })(this,idx);                            
                });
                $this.attr('data-status',"closed");
                $this.addClass('rs-circle-down').removeClass('rs-circle-up');
            }else{
                ths.dom.find( "[data-selector^=" + selector + "_]" ).each(function(idx){
                    (function(ths,idx){
                        setTimeout(function(){
                            $(ths).show();
                        },10*idx);
                    })(this,idx);  
                    $this.attr('data-status',"closed");
                    $this.addClass('rs-circle-down').removeClass('rs-circle-up');
                });
                $this.attr('data-status',"opened");
                $this.addClass('rs-circle-up').removeClass('rs-circle-down');
            }
        };
        var subn = function( sequence, selector, $item ){

            var children=ths.dom.find( "[data-depth=" + selector.substring(0,selector.length-1) +(sequence-2)+ "]" );
            
            var _n=$item.clone(),
            level=parseInt($item.data("level")||$item.attr("data-level")),
            id=$item.data("id")||$item.attr("data-id"),
            subn=parseInt($item.data("subn")||$item.attr("data-subn"));
            level+=1;
            _n.attr("data-level",level);
            _n.attr("data-id","");
            if(level==3){
                _n.find("i.subn").addClass('not-allowed');
            }
            //ths.dom.find(["data-suquence="+i])
            _n.insertAfter($item);

            //修正节点上的直接子节点的个数
            $item.attr( "data-sub_num", children.length );
            

            $item.data("subn",subn+1)||$item.attr("subn",subn+1);
            var preobj=ths.preText(level,subn);

            _n.find(".treelist-item").remove();
            _n.find("i").removeClass('done').removeClass('pass').removeClass('warning').removeClass('error');
            _n.find(".field-text label").html(preobj.label);
            _n.find(".field-text input").removeAttr("readonly").val("").attr("placeholder",preobj.placeholder);
            // _n.addClass('presymbol');
            
            _n.css("margin-left",(level*ths.settings.prewidth)+"px");
            _n.find("input").focus();
        };
        var up = function( elm, cls ){

            var $elm = $( elm );

            if( elm.tagName.toUpperCase() === "HTML" ){
                return console.log( "没有找到相关元素" );
            }else if( $elm.hasClass( cls ) ){
                return elm;
            }else{
                return arguments.callee( elm.parentNode, cls );
            }
        };        
        
        this.dom.on( "click",".tools-operation .operation i", function( ){
            
            if( $(this).hasClass( 'not-allowed' ) ){
                return ths.dom;
            }

            var cmd = $(this).data( "cmd" ) || $(this).attr( "data-cmd" );
            var item = up( this, "treelist-item" );
            _ievent.call( this, cmd, item );
        });
        this.dom.on( "click", ".tap", function(){
            var $item = $(this.parentNode);
            $(this).addClass('active');
            var index=parseInt($item.attr("data-index"));
            var selector=$item.data("selector")||$item.attr("data-selector");
            selector=selector.substring(0,selector.length-1);
            var att=[];
            ths.dom.find("[data-selector^="+selector+"]").each(function(){
                var $this=$(this);
                if($this.hasClass("treelist-leaf-att")){
                    att.push(this);
                }
            });
            att.forEach(function(elm,idx){
                if($(elm).attr("data-index")!=index){
                    $(elm).find(".tap").removeClass("active");
                }
            });
            ths.attEdit.call(ths,$item.attr("data-id"),$item,{});
            
        });
        this.dom.on( "click", ".tools-operation .crud i", function(){

            if( $(this).hasClass( 'not-allowed' ) ){
                return ths.dom;
            }

            var cmd = $(this).data( "cmd" ) || $( this ).attr( "data-cmd" );
            var item = up( this, "treelist-item" );
            _ievent.call( this, cmd, item );

        });

        this.dom.find( ".treelist-item:first-child i.del" ).addClass( 'not-allowed' );
    };
    treelist.prototype.attEdit=function(id,$item,data){
        var ths=this;
        var _temp="<div class='mask'>";
          _temp +='<div class="edit-att">';
          _temp +='<div class="add rs-add"></div>';
          _temp +='<ul class="nav nav-tabs">';
            _temp +='<li data-cmd="daoxue" class="active daoxue">导学</li>';
            _temp +='<li data-cmd="testing" class="testing">测评</li>';
            _temp +='<li data-cmd="material" class="material">资料下载</li>';
          _temp +='</ul>';
          _temp +='<div class="tab-content">';
            _temp +='<div class="tab-pane daoxue active">';
                _temp +='<table class="table">';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="导学名称"/></td>';
                _temp +='<td><select name="address" ><option value="">地址1</option></select></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="导学名称"/></td>';
                _temp +='<td><select name="address" ><option value="">地址1</option></select></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="导学名称"/></td>';
                _temp +='<td><select name="address" ><option value="">地址1</option></select></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='</table>';
            _temp +='</div>';
            _temp +='<div class="tab-pane testing">';
                _temp +='<table class="table">';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="链接"/></td>';
                _temp +='<td><input type="text"/></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="链接"/></td>';
                _temp +='<td><input type="text"/></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="链接"/></td>';
                _temp +='<td><input type="text"/></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='</table>';
            _temp +='</div>';
            _temp +='<div class="tab-pane material">';
                _temp +='<table class="table">';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="资料名称"/></td>';
                _temp +='<td><input type="file" name="material"></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="资料名称"/></td>';
                _temp +='<td><input type="file" name="material"></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr> ';
                _temp +='<tr class="item">';
                _temp +='<td><input type="text" placeholder="资料名称"/></td>';
                _temp +='<td><input type="file" name="material"></td>';
                _temp +='<td><span class="btn-del">删除</span></td>';
                _temp +='</tr>';
                _temp +='</table>';
            _temp +='</div>';
          _temp +='</div>';
            _temp +='<div class="tools btn-group">';
                _temp +='<span class="btn sure">保存</span><span class="btn cancel">取消</span>';
            _temp +='</div>';
        _temp +='</div></div>';
        $(".mask").remove();
        var edit=$(_temp).appendTo('body');
        edit.on("click",".add",function(){
            var table=edit.find(".tab-content .tab-pane.active");
            var tpl=table.find(".item").eq(0);
            var newDom=tpl.clone();
            newDom.find("select,input").each(function(){
                if(this.tagName.toUpperCase()==="SELECT"){
                    $(this).val("0");
                }else{
                    $(this).val("");
                }
            });
            newDom.appendTo(table.find("tbody"));
        });
        edit.on("click",".cancel",function(){
            edit.remove();
        });
        edit.on("click",".btn-del",function(){
            if(confirm("确定删除本条记录吗？")){
                var table=edit.find(".tab-content .tab-pane.active");
                if(table.find(".item").length<=1){
                    alert("删除失败,不能删除最后一条记录");
                    return ;
                }
                $(this.parentNode.parentNode).remove();
            }
        });
        edit.on("click",".nav-tabs li",function(){
            var $this=$(this);
            if(!$this.hasClass('active')){
                $this.siblings('li').removeClass('active');
                $this.addClass('active');
                var cmd=$this.attr("data-cmd");
                edit.find(".tab-content .tab-pane."+cmd).addClass('active').siblings(".tab-pane").removeClass('active');
            }
        });
        function addAtt(url,data){
            $.ajax({
                url:"./data/addAtt.json",
                data:data,
                success:function(e){
                    if(e.code){
                        console.log("处理失败");
                    }else{
                        alert("保存成功");
                        ths.insertATT(id,
                            e.data,
                            (function(){
                                var sel=$item.attr("data-selector");
                                return sel.substring(0,sel.length-2);
                            })(),
                            parseInt((function(){
                                var sel=$item.attr("data-selector");
                                return sel.substr(sel.length-1,1);
                            })()),
                            function(){
                                edit.remove();
                        });

                    }
                },
                error:function(){

                }
            });
        }
        edit.on("click",".sure",function(){
            var data={},url="";
            var tpl=edit.find("tab-content .tab-pane.active");
            if(tpl.hasClass('daoxue')){
                
            }else if(tpl.hasClass('testing')){
                
            }else if(tpl.hasClass('material')){
                
            }
            addAtt(url,data);
        });

    };
    treelist.prototype.insertATT=function(pid,data,selector,num,fn){
        var str=this.draw.drawTreeATT(data[0].parent_id,data,selector,num);
        fn&&fn();
    };
    treelist.prototype.deleteItem = function( id, url, fn ){
        
        var ths=this;
        
        $.ajax({
            url:url,
            data:{
                id:id
            },
            success:function( data ){
                if( data.code !== 0 ){
                    return console.log( "error code:" + data.code + ", " + data.msg );
                }                
                fn && fn.call( ths, "success" );
            },
            error:function(){
                console.log( "删除出错" );
                fn && fn.call( ths, "error" );
            }
        });
    };
    treelist.prototype.setValue=function(ops){
        this.init(ops);
    };
    treelist.prototype.getValue=function($elm){

    };
    $.extend(true, treelist.prototype, method);
    
    $(function(){
        $("[data-com='treelistexd']").Treelist({
            url:"./data/data_course_treelist.json",
            method:{

            }
        });
    });
});
