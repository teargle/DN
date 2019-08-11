function revertManage( block ) {
	$(".manage-item").addClass('hidden');
	$('.' + block).removeClass('hidden');
}

function resetTable(href) {
  $("#btn-new").removeClass("hidden");
  $("#btn-new a").attr('href', href);
  $("#manage_list").bootstrapTable('destroy');
  $("#manage_list").html('');
}
var maxMobileBannerId = 0;
var maxWebBannerId = 0;
function revertIndex() {
  $("#btn-new").addClass('hidden');
  $("#manage_list").bootstrapTable('destroy');
  $("#manage_list").html('');

  var $home = $("#home_manage").clone();
  $("#manage_list").append($home);
  
  $.post("/admin/manage/home", {} ,
      function(data){
        data = $.parseJSON(data);
        if( data.result ) {
          var dict = data.obj ;
          $.each(dict , function ( idx , item) {
            if(item ['name'] == 'banner_main_title') {
              $("input[name='banner_main_title']").val(item ['value']);
            }
            if(item ['name'] == 'banner_sub_title') {
              $("input[name='banner_sub_title']").val(item ['value']);
            }
            var arr = item ['name'].split('__');
            if( arr [0] == 'banner_web_img' ) {
              var webImg = $("#web_input_template").clone();
              webImg.find(".control-label").html("网页图片：");
              webImg.removeAttr('id');
              webImg.find('input').attr('name' , item ['name']).val(item ['value']);
              webImg.insertBefore("#separate");
              maxWebBannerId = maxWebBannerId > parseInt(arr[1]) ? maxWebBannerId : parseInt(arr [1]);
            }
            if( arr [0] == 'banner_mobile_img' ) {
              var webImg = $("#web_input_template").clone();
              webImg.find(".control-label").html("网页图片：");
              webImg.removeAttr('id');
              webImg.find('input').attr('name' , item ['name']).val(item ['value']);
              webImg.insertBefore("#up_banner_input_area");
              maxMobileBannerId = maxMobileBannerId > parseInt(arr[1]) ? maxMobileBannerId : parseInt(arr [1]);
            }
            if(item['name'] == 'st_practice' ) {
              $("input[name='st_practice']").val(item ['value']);
            }
            if(item['name'] == 'st_market' ) {
              $("input[name='st_market']").val(item ['value']);
            }
            if(item['name'] == 'st_sale' ) {
              $("input[name='st_sale']").val(item ['value']);
            }
            if(item['name'] == 'st_think' ) {
              $("input[name='st_think']").val(item ['value']);
            }
          });
        } else {
          console.log( "加载失败" );
        }
  });
}

function revertProduct() {
resetTable('/admin/manage/edit_product/id/0');
$("#manage_list").bootstrapTable({ // 对应table标签的id
      url: "/admin/manage/product", // 获取表格数据的url
      cache: false, // 设置为 false 禁用 AJAX 数据缓存， 默认为true
      striped: true,  //表格显示条纹，默认为false
      pagination: true, // 在表格底部显示分页组件，默认false
      pageList: [10, 20], // 设置页面可以显示的数据条数
      pageSize: 10, // 页面数据条数
      pageNumber: 1, // 首页页码
      sidePagination: 'server', // 设置为服务器端分页
      queryParams: function (params) { // 请求服务器数据时发送的参数，可以在这里添加额外的查询参数，返回false则终止请求
     	 // Add params
     	 return {
              pageSize: params.limit, // 每页要显示的数据条数
              offset: params.offset, // 每页显示数据的开始行号
              sort: params.sort, // 要排序的字段
              sortOrder: params.order, // 排序规则
          }
      },
      sortName: 'id', // 要排序的字段
      sortOrder: 'desc', // 排序规则
      columns: [
          {
              field: 'id', // 返回json数据中的name
              title: 'ID', // 表格表头显示文字
              align: 'center', // 左右居中
              valign: 'middle' // 上下居中
          }, {
              field: 'title',
              title: '名称',
              align: 'left',
              valign: 'middle'
          }, {
              field: 'short_desc',
              title: '短描述',
              align: 'left',
              valign: 'middle'
          }, {
              field: 'category_title',
              title: '分类',
              align: 'left',
              valign: 'middle'
          }, {
               title: "操作",
              align: 'center',
              valign: 'middle',
              width: 160, // 定义列的宽度，单位为像素px
              formatter: function (value, row, index) {
                  return '<a href="/admin/manage/edit_product/id/'+row.id+'" class="btn btn-default btn-lg" role="button">编辑</a>';
              }
          }
      ],
      onLoadSuccess: function(){  //加载成功时执行
            console.info("加载成功");
      },
      onLoadError: function(){  //加载失败时执行
            console.info("加载数据失败");
      }

}) ;
}

$(document).ready(function() {
    $('#short_desc').trumbowyg();
    $('#description').trumbowyg();
    $('#main_body').trumbowyg();
});


function revertCategory() {
resetTable('/admin/manage/edit_category/id/0');
$("#manage_list").bootstrapTable({ // 对应table标签的id
      url: "/admin/manage/category", // 获取表格数据的url
      cache: false, // 设置为 false 禁用 AJAX 数据缓存， 默认为true
      striped: true,  //表格显示条纹，默认为false
      pagination: true, // 在表格底部显示分页组件，默认false
      pageList: [10, 20], // 设置页面可以显示的数据条数
      pageSize: 10, // 页面数据条数
      pageNumber: 1, // 首页页码
      sidePagination: 'server', // 设置为服务器端分页
      queryParams: function (params) { // 请求服务器数据时发送的参数，可以在这里添加额外的查询参数，返回false则终止请求
       // Add params
       return {
              pageSize: params.limit, // 每页要显示的数据条数
              offset: params.offset, // 每页显示数据的开始行号
              sort: params.sort, // 要排序的字段
              sortOrder: params.order, // 排序规则
          }
      },
      sortName: 'id', // 要排序的字段
      sortOrder: 'desc', // 排序规则
      columns: [
          {
              field: 'seq', // 返回json数据中的name
              title: 'ID', // 表格表头显示文字
              align: 'center', // 左右居中
              valign: 'middle' // 上下居中
          }, {
              field: 'title',
              title: '名称',
              align: 'left',
              valign: 'middle'
          }, {
              field: 'detail',
              title: '短描述',
              align: 'left',
              valign: 'middle'
          }, {
               title: "操作",
              align: 'center',
              valign: 'middle',
              width: 160, // 定义列的宽度，单位为像素px
              formatter: function (value, row, index) {
                  return '<a href="/admin/manage/edit_category/id/'+row.id+'" class="btn btn-default btn-lg" role="button">编辑</a>';
              }
          }
      ],
      onLoadSuccess: function(){  //加载成功时执行
            console.info("加载成功");
      },
      onLoadError: function(){  //加载失败时执行
            console.info("加载数据失败");
      }

}) ;
}

function revertIntro() {
resetTable('/admin/manage/edit_intro/id/0');
$("#manage_list").bootstrapTable({ // 对应table标签的id
      url: "/admin/manage/intro", // 获取表格数据的url
      cache: false, // 设置为 false 禁用 AJAX 数据缓存， 默认为true
      striped: true,  //表格显示条纹，默认为false
      pagination: true, // 在表格底部显示分页组件，默认false
      pageList: [10, 20], // 设置页面可以显示的数据条数
      pageSize: 10, // 页面数据条数
      pageNumber: 1, // 首页页码
      sidePagination: 'server', // 设置为服务器端分页
      queryParams: function (params) { // 请求服务器数据时发送的参数，可以在这里添加额外的查询参数，返回false则终止请求
       // Add params
       return {
              pageSize: params.limit, // 每页要显示的数据条数
              offset: params.offset, // 每页显示数据的开始行号
              sort: params.sort, // 要排序的字段
              sortOrder: params.order, // 排序规则
          }
      },
      sortName: 'sequence', // 要排序的字段
      sortOrder: 'desc', // 排序规则
      columns: [
          {
              field: 'sequence', // 返回json数据中的name
              title: 'ID', // 表格表头显示文字
              align: 'center', // 左右居中
              valign: 'middle' // 上下居中
          }, {
              field: 'title',
              title: '名称',
              align: 'left',
              valign: 'middle'
          }, {
               title: "操作",
              align: 'center',
              valign: 'middle',
              width: 160, // 定义列的宽度，单位为像素px
              formatter: function (value, row, index) {
                  return '<a href="/admin/manage/edit_intro/id/'+row.id+'" class="btn btn-default btn-lg" role="button">编辑</a>';
              }
          }
      ],
      onLoadSuccess: function(){  //加载成功时执行
            console.info("加载成功");
      },
      onLoadError: function(){  //加载失败时执行
            console.info("加载数据失败");
      }

}) ;
}


function revertNews() {
resetTable('/admin/manage/edit_news/id/0');
$("#manage_list").bootstrapTable({ // 对应table标签的id
      url: "/admin/manage/news", // 获取表格数据的url
      cache: false, // 设置为 false 禁用 AJAX 数据缓存， 默认为true
      striped: true,  //表格显示条纹，默认为false
      pagination: true, // 在表格底部显示分页组件，默认false
      pageList: [10, 20], // 设置页面可以显示的数据条数
      pageSize: 10, // 页面数据条数
      pageNumber: 1, // 首页页码
      sidePagination: 'server', // 设置为服务器端分页
      queryParams: function (params) { // 请求服务器数据时发送的参数，可以在这里添加额外的查询参数，返回false则终止请求
       // Add params
       return {
              pageSize: params.limit, // 每页要显示的数据条数
              offset: params.offset, // 每页显示数据的开始行号
              sort: params.sort, // 要排序的字段
              sortOrder: params.order, // 排序规则
          }
      },
      sortName: 'id', // 要排序的字段
      sortOrder: 'desc', // 排序规则
      columns: [
          {
              field: 'id', // 返回json数据中的name
              title: 'ID', // 表格表头显示文字
              align: 'center', // 左右居中
              valign: 'middle' // 上下居中
          }, {
              field: 'title',
              title: '名称',
              align: 'left',
              valign: 'middle'
          }, {
               title: "操作",
              align: 'center',
              valign: 'middle',
              width: 160, // 定义列的宽度，单位为像素px
              formatter: function (value, row, index) {
                  return '<a href="/admin/manage/edit_news/id/'+row.id+'" class="btn btn-default btn-lg" role="button">编辑</a>';
              }
          }
      ],
      onLoadSuccess: function(){  //加载成功时执行
            console.info("加载成功");
      },
      onLoadError: function(){  //加载失败时执行
            console.info("加载数据失败");
      }

}) ;
}

function revertSetting() {
  $("#btn-new").addClass('hidden');
  $("#manage_list").bootstrapTable('destroy');
  $("#manage_list").html('');

  var $home = $("#setting_manage").clone();
  $("#manage_list").append($home);
  
  $.post("/admin/manage/setting", {} , function(data){
        data = $.parseJSON(data);
        if( data.result ) {
          
        } else {
          console.log( "加载失败" );
        }
  });
}


function revertFeature() {
  resetTable('/admin/manage/edit_feature/id/0');
  $("#manage_list").bootstrapTable({ // 对应table标签的id
        url: "/admin/manage/feature", // 获取表格数据的url
        cache: false, // 设置为 false 禁用 AJAX 数据缓存， 默认为true
        striped: true,  //表格显示条纹，默认为false
        pagination: true, // 在表格底部显示分页组件，默认false
        pageList: [10, 20], // 设置页面可以显示的数据条数
        pageSize: 10, // 页面数据条数
        pageNumber: 1, // 首页页码
        sidePagination: 'server', // 设置为服务器端分页
        queryParams: function (params) { // 请求服务器数据时发送的参数，可以在这里添加额外的查询参数，返回false则终止请求
         // Add params
         return {
                pageSize: params.limit, // 每页要显示的数据条数
                offset: params.offset, // 每页显示数据的开始行号
                sort: params.sort, // 要排序的字段
                sortOrder: params.order, // 排序规则
            }
        },
        sortName: 'id', // 要排序的字段
        sortOrder: 'desc', // 排序规则
        columns: [
            {
                field: 'id', // 返回json数据中的name
                title: 'ID', // 表格表头显示文字
                align: 'center', // 左右居中
                valign: 'middle' // 上下居中
            }, {
                field: 'title',
                title: '名称',
                align: 'left',
                valign: 'middle'
            }, {
                 title: "操作",
                align: 'center',
                valign: 'middle',
                width: 160, // 定义列的宽度，单位为像素px
                formatter: function (value, row, index) {
                    return '<a href="/admin/manage/edit_feature/id/'+row.id+'" class="btn btn-default btn-lg" role="button">编辑</a>';
                }
            }
        ],
        onLoadSuccess: function(){  //加载成功时执行
              console.info("加载成功");
        },
        onLoadError: function(){  //加载失败时执行
              console.info("加载数据失败");
        }

  }) ;
}
