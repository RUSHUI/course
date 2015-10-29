define(["jquery"], function($) {
    var method=null;
    $.fn.Treelist=function(ops){
        method=ops.method;
        return this.each(function(idx,elm){
            //console.log(this,idx,elm);
            var $elm=$(elm);
            var pid=$(elm).data("id")||$(elm).attr("data-id"),
            name=$(elm).data("name")||$(elm).attr("data-name"),
            data=$(elm).data("init")||$(elm).attr("data-init");
            if(typeof data ==="string"){
                data=$.parseJSON(data);
            }
            var settings=$.extend(true,{},$.fn.Treelist.defaults,{pid:pid,name:name,data:data}, ops||{});
            this.rs={
                treelist:new treelist($(this),settings)
            };
        });        
    };
    $.fn.Treelist.defaults={
        url:"",//数据地址
        request:true ,//是否请求数据
        data:[],
        pid:+new Date,
        name:"treelist",
        prewidth:35,
        vedioicon:true,
        delUrl:"./data/del_course_treelist.json",
        preText:{
            0:"",
            1:"部分",
            2:"章",
            3:"节"
        },
        chinesenum:["一","二","三","四","五","六","七","八","九","十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十"]

    };
    function treelist(elm,ops){
        this.dom=elm;
        this._init=0;
        this.treelist=this.init(ops);
    }
    treelist.prototype.init=function(ops){
        // console.log(this);
        this.settings=ops;
        if(this._init!==0){
            return;
        }
        this._init=1;
        if(ops.request){
            this.postData(function(data){
                this.render(data);
                this.regEvent();
            });
        }else{
            this.render(ops.data);
            this.regEvent();
        }
        return this;
    };
    treelist.prototype.preTextupdate=function(_level,_len){
        var chinesenum=this.settings.chinesenum;
        return {
            label:"第"+chinesenum[_len]+ this.settings.preText[_level]+"：",
            placeholder:"请输入本"+this.settings.preText[_level]+"标题"
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
            success:function(data){
                if(data.code!=0){
                    return console.log("error code:"+ datacode + ", "+data.msg);
                }                
                fn&&fn.call(ths,data.data);
            },
            error:function(data){
                console.log("获取数据出错");
            }
        });
    };
    treelist.prototype.render=function(data){
        var ths=this;
        var str="";
        var draw=function _draw(data){
            var _temp="",num=0,label="";
            if(data.length>0){
                for(var i=0;i<data.length;i++){
                    var marginLeft=data[i].level*ths.settings.prewidth;
                    _temp+='<div data-childnum='+data[i].subn.length+' style="margin-left:'+marginLeft+'px" data-level="'+data[i].level+'" data-id="'+data[i].id+'" class="treelist-item nroot">';
                    var preobj=ths.preTextupdate(data[i].level,i);
                    if(data[i].level==0){
                        preobj.label=data[i].text;
                    }
                    _temp+='<div class="field-text"><label>'+preobj.label+'</label><input readonly type="text" placeholder="'+preobj.placeholder+'" class="form-field" value="'+data[i].text+'"/></div>';
                    _temp+='<div class="tools-operation ';
                    if(ths.settings.vedioicon){
                        _temp+='vedio">';
                    }else{
                        _temp+='">';
                    }
                    _temp+='<span class="operation">';
                    _temp+='<i data-cmd="subn" class="subn rs-list3" title="添加内容"></i>';
                    _temp+='<i data-cmd="nlgpt" class="nlgpt rs-clipboard3 rs-clipboard" title="添加知识点"></i>';
                    _temp+='<i data-cmd="attachment" class="attachment rs-attachment2" title="添加附件"></i>';
                    _temp+='<i data-cmd="testlisten" class="testlisten rs-volume4" title="选为试听"></i>';
                    _temp+='<i data-cmd="write" class="write rs-pencil4" title="编辑内容"></i>';
                    _temp+='</span><span class="crud" style="margin-left:10px;">';
                   // _temp+='<i class="setup  rs-cogs rs-settings2" title="配置记录"></i>';
                    _temp+='<i data-cmd="save" class="save rs-download" title="保存记录"></i>';
                    _temp+='<i data-cmd="del" class="del rs-trashcan" title="删除记录"></i>';
                    _temp+='<i data-cmd="state" class="state rs-checkmark2" title="初始状态"></i>';
                    _temp+='</span></div>';

                    _temp+='<div class="clear-float"></div>';
                    _temp+="</div>";                    
                }
            }else{
                return _temp;
            }
            return _temp;
        };
        str+=draw(data);
        this.dom.html(str);
        return this;
    };
    treelist.prototype.remove=function(){
        this.dom.empty();
        return this;
    };

    treelist.prototype.regEvent=function(){
        var ths=this;
        var _ievent=function(cmd,item){
            var $item=$(item),$this=$(this);
            switch(cmd){
                case "subn" :
                    subn.call(this,cmd,$item);
                break;
                case "nlgpt" :

                break;
                case "attachment" :

                break;
                case "testlisten" :
                    $item.find(">.tools-operation i.testlisten").toggleClass('pass').toggleClass('done');
                break;
                case "write" :
                    if(!$this.hasClass('done')){
                        $item.find(">.field-text input").removeAttr("readonly");
                        $this.addClass('done');
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
        var subn=function(cmd,$item){
            var _n=$item.clone(),level=parseInt($item.data("level")||$item.attr("data-level"));
            level+=1;
            _n.attr("data-level",level);
            if(level==3){
                _n.find("i.subn").addClass('not-allowed');
            }
            var len=_n.find(">.treelist-item").length;
            var preobj=ths.preTextupdate(level,len);
            _n.find(".treelist-item").remove();
            _n.find("i").removeClass('done').removeClass('pass').removeClass('warning').removeClass('error');
            _n.find(".field-text label").html(preobj.label);
            _n.find(".field-text input").removeAttr("readonly").val("").attr("placeholder",preobj.placeholder);
            // _n.addClass('presymbol');
            _n.insertAfter($item);
            _n.css("margin-left",(level*ths.settings.prewidth)+"px");
            _n.find("input").focus();
        };
        var up=function(elm,cls){
            var $elm=$(elm);
            if(elm.tagName.toUpperCase()==="HTML"){
                return console.log("没有找到相关元素");
            }else if($elm.hasClass(cls)){
                return elm;
            }else{
                return arguments.callee( elm.parentNode,cls);
            }
        };
        this.dom.on("click",".treelist-item>.tools-operation .operation i",function(){
            if($(this).hasClass('not-allowed')){
                return;
            }
            var cmd=$(this).data("cmd")||$(this).attr("data-cmd");
            var item=up(this,"treelist-item");
            _ievent.call(this,cmd,item);
        });
        this.dom.on("click",".treelist-item>.tools-operation .crud i",function(){
            var cmd=$(this).data("cmd")||$(this).attr("data-cmd");
            var item=up(this,"treelist-item");
            _ievent.call(this,cmd,item);
        });
    };
    treelist.prototype.deleteItem=function(id,url,fn){
        var ths=this;
        $.ajax({
            url:url,
            data:{
                id:id
            },
            success:function(data){
                if(data.code!=0){
                    return console.log("error code:"+ data.code + ", "+data.msg);
                }                
                fn&&fn.call(ths,"success");
            },
            error:function(){
                console.log("删除出错");
                fn&&fn.call(ths,"error");
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
        $(".treelistexd").Treelist({
            url:"./data/data_course_treelist.json",
            method:{

            }
        });
    });
});
