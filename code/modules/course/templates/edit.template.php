<div class="current-position">当前位置：<span>课程制作（组课）</span></div>
<div class="select-course-type">
    <span>选取课程类型</span>
    <label for=""><input type="radio" name="course_type">预售课程</label>
    <label for=""><input type="radio" name="course_type">在销课程</label>
    <label for=""><input type="radio" name="course_type">在销待补</label>
</div>
<div class="select-course">
    <div class="select-course-title">这是课程标题</div>
    <div class="treelistexd" data-name="这是课程标题" data-id="title-course" data-init="{"a":1}">
        <div class="treelist-item nroot rw">
            <div class="field-text">
                <input type="text" class="form-field" value="这是课程标题"/>
            </div>
            <div class="tools-operation">            
                <i class="subn operation rs-list3" title="添加内容"></i>
                <i class="nlgpt operation rs-clipboard3 rs-clipboard" title="添加知识点"></i>
                <i class="attachment operation rs-attachment2" title="添加附件"></i>
                <i class="write operation rs-pencil4" title="编辑内容"></i>
                <i class="add crud rs-checkmark2" title="保存记录"></i>
                <i class="del crud  rs-trashcan" title="删除记录"></i>
            </div>            
            <div class="extra  clear-float">
                
            </div>
            <div class="posline">
                <i class="vertical"></i>
                <i class="horizontal"></i>
            </div>         
            <div class="treelist-item nroot rw">
            
                <div class="field-text">
                    <input type="text" class="form-field" value="这是课程标题"/>
                </div>
                <div class="tools-operation">            
                    <i class="subn operation rs-list3" title="添加内容"></i>
                    <i class="nlgpt operation rs-clipboard3 rs-clipboard" title="添加知识点"></i>
                    <i class="attachment operation rs-attachment2" title="添加附件"></i>
                    <i class="write operation rs-pencil4" title="编辑内容"></i>
                    <i class="add crud rs-checkmark2" title="保存记录"></i>
                    <i class="del crud  rs-trashcan" title="删除记录"></i>
                </div>            
                <div class="extra  clear-float">
                    
                </div>
                <div class="posline">
                    <i class="vertical"></i>
                    <i class="horizontal"></i>
                </div>            
            </div>   
        </div>
    </div>
</div>

<form class="form-horizontal" role="form" action="<?php echo $this->generateURL('Course.Course',array('action'=>'add'))?>" method="post" enctype="multipart/form-data">
    <div class = 'form-group'>
        <label class="col-sm-2 control-label">选取课程类型：</label>
        <div>
            <label class="checkbox-inline">
                <input type="radio" checked name="sale_type" value='1'/>预售
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="sale_type" value="2"/>在销
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="sale_type" value="3"/>在销待补
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">课程名称：</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <?php echo '2015年浙江银行考试-精品班-金融学';?>
            </p>
        </div>
        <input type="hidden" name="course_id" value="<?php echo 1;?>"/>
    </div>
    <div class="form-group">
        <div class="col-sm-5">
            <input type="text" class="form-control" name="name" value="2015年浙江银行考试-精品班-金融学"/>
        </div>
        <div class="col-sm-7">
            <div class="btn-group col-sm-7">
                <button type="button" class="btn btn-sm btn-default addchildren">+子目录</button>
                <button type="button" class="btn btn-sm btn-default addkps">+知识点</button>
                <button type="button" class="btn btn-sm btn-default addappend">+资料附件</button>
                <button type="button" class="btn btn-sm btn-default ckaudition">选为试听</button>
            </div>
            <div class="col-sm-1">
                <!--<img src="./uploads/viewshow.png" width='20px' heigth="20px" class="img-rounded" title="图标" />-->
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" value="请选择参考课程">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-1">
            <input type="text" class="form-control" name="name" value="第一部分"/>
        </div>
        <div class="col-sm-5">
            <div class="btn-group col-sm-9">
                <button type="button" class="btn btn-sm btn-default addchildren">+子目录</button>
                <button type="button" class="btn btn-sm btn-default addkps">+知识点</button>
                <button type="button" class="btn btn-sm btn-default addappend">+资料附件</button>
                <button type="button" class="btn btn-sm btn-default ckaudition">选为试听</button>
            </div>
            <div class="col-sm-3">
                <!--<img src="./uploads/viewshow.png" width='20px' heigth="20px" class="img-rounded" title="图标" />-->
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2">
            <input type="text" class="form-control" name="name" value="第一章"/>
        </div>
        <div class="col-sm-5">
            <div class="btn-group col-sm-9">
                <button type="button" class="btn btn-sm btn-default addchildren">+子目录</button>
                <button type="button" class="btn btn-sm btn-default addkps">+知识点</button>
                <button type="button" class="btn btn-sm btn-default addappend">+资料附件</button>
                <button type="button" class="btn btn-sm btn-default ckaudition">选为试听</button>
            </div>
            <div class="col-sm-3">
                <!--<img src="./uploads/viewshow.png" width='20px' heigth="20px" class="img-rounded" title="图标" />-->
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-3">
            <input type="text" class="form-control" name="name" value="第一节"/>
        </div>
        <div class="col-sm-5">
            <div class="btn-group col-sm-9">
                <button type="button" class="btn btn-sm btn-default addchildren">+子目录</button>
                <button type="button" class="btn btn-sm btn-default addkps">+知识点</button>
                <button type="button" class="btn btn-sm btn-default addappend">+资料附件</button>
                <button type="button" class="btn btn-sm btn-default ckaudition">选为试听</button>
            </div>
            <div class="col-sm-3">
                <!--<img src="./uploads/viewshow.png" width='20px' heigth="20px" class="img-rounded" title="图标" />-->
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-3">
            <input type="text" class="form-control" name="name" value="知识点"/>
        </div>
        <div class="col-sm-5">
            <div class="btn-group col-sm-9">
                <button type="button" class="btn btn-sm btn-default addchildren">+知识点资料附件</button>
                <button type="button" class="btn btn-sm btn-default addappend">+资料附件</button>
                <button type="button" class="btn btn-sm btn-default ckaudition">选为试听</button>
            </div>
            <div class="col-sm-3">
                <!--<img src="./uploads/viewshow.png" width='20px' heigth="20px" class="img-rounded" title="图标" />-->
            </div>
        </div>
    </div>
</form>