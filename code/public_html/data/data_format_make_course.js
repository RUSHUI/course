{
    "code":0,
    "msg":"返回成功",
    "data":[
        {   
            "id":1,              //记录标识
            "parent_id":0,    //依附节点id  应该是目录节点id          
            "type":0,         //type指定节点类型，0->目录  1->知识点  2->附件
            "name":"章节名称",//目录名字
            "depth":0,        //当前节点的目录深度  影响前端显示该节点是 部分、章还是节
            "status":1,       //课程销售类型(1,预售 2,在销 3,在销待补)
            "subn":0,         //直接子（儿子）目录的个数（不包括知识点和附件）
            "is_audition":1,  //是否为试听(0不试听，1试听)
            "weight":1,       //顺序
            "course_id":1,    //课程id
            "sub_childs":[
                {"id":2,"parent_id":1,"type":0,"name":"章节名称","depth":0,"status":1,"sub_childs":0,"is_audition":1,"weight":1,"course_id":1},
                {"id":3,"parent_id":1,"type":0,"name":"章节名称","depth":0,"status":1,"sub_childs":0,"is_audition":1,"weight":1,"course_id":1},
                {"id":4,"parent_id":1,"type":2,
                    "attachments_type":1,//附件类型(1,导学[视频] 2,测评[]3,资料[下载类])
                    "name":"附件名称",
                    "course_catalog_id":1,//本id === parent_id  ===  依附目录节点id
                    "data_path":"url",//附件地址
                    "weight":1,"create_time":"2015-11-08 11:11:01"//附件创建时间
                },
                {"id":5,"parent_id":1,"type":2,"attachments_type":1,"name":"附件名称","course_catalog_id":1,"data_path":"url","weight":1,"create_time":"2015-11-08 11:11:01"},
                {"id":6,"parent_id":1,"type":2,"attachments_type":1,"name":"附件名称","course_catalog_id":1,"data_path":"url","weight":1,"create_time":"2015-11-08 11:11:01"},
                {"id":7,"parent_id":0,"type":1,"name":"知识点名称","course_catalog _id":0,"weight":1},
                {"id":8,"parent_id":1,"type":2,"attachments_type":1,"name":"附件名称","course_catalog_id":1,"data_path":"url","weight":1,"create_time":"2015-11-08 11:11:01"}
            ]
        }
    ]
}