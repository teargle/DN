function revertManage( block ) {
	$(".manage-item").addClass('hidden');
	$('.' + block).removeClass('hidden');
}

function resetTable(href) {
  $("#btn-new").removeClass("hidden");
  $("#btn-new a").attr('href', href);
  $("#manage_list").bootstrapTable('destroy');
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
});

function addProp () {
  var $prop = $("#prop-template").clone().removeClass('hidden') ;
  $prop.removeAttr('id');
  $prop.find('input').val('');
  var $props = $("#props") ;
  $props.append($prop);
}

function delProp (obj) {
  $(obj).closest('.row').remove();
}

function saveProduct() {
  var params = $("#product-from").serializeArray();
  var $props = $("#props .row") ;
  var prop = {};
  $props.each(function(){
      var n = $(this).find('.prop-name').val();
      var value = $(this).find('.prop-content').val();
      prop[n] = value ;
  });
  params.prop = prop;
  $.post("/admin/manage/saveProduct", {
         params : params,
         prop : prop,

      } ,
      function(data){
        data = $.parseJSON(data);
        if( data.result ) {
          document.location.reload();
        } else {
           alert( data.message ) ;
        }
  });
}

function delProduct( id ) {
  $.post("/admin/manage/delProduct", {
         id : id
      } ,
      function(data){
        data = $.parseJSON(data);
        if( data.result ) {
           window.location.href = "/admin/manage/index";
        } else {
           alert( data.message ) ;
        }
  });
}

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

function saveCategory() {
  var params = $("#category-from").serializeArray();
  $.post("/admin/manage/saveCategory",  params ,
      function(data){
        data = $.parseJSON(data);
        if( data.result ) {
          document.location.reload();
        } else {
           alert( data.message ) ;
        }
  });
}

function delCategory( id ) {
  $.post("/admin/manage/delCategory", {
         id : id
      } ,
      function(data){
        data = $.parseJSON(data);
        if( data.result ) {
           window.location.href = "/admin/manage/index";
        } else {
           alert( data.message ) ;
        }
  });
}

function revertIntro() {
$("#btn-new").addClass("hidden");
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

function saveIntro() {
  var params = $("#intro-from").serializeArray();
  $.post("/admin/manage/saveIntro",  params ,
      function(data){
        data = $.parseJSON(data);
        if( data.result ) {
          document.location.reload();
        } else {
           alert( data.message ) ;
        }
  });
}

function delIntro( id ) {
  $.post("/admin/manage/delIntro", {
         id : id
      } ,
      function(data){
        data = $.parseJSON(data);
        if( data.result ) {
           window.location.href = "/admin/manage/index";
        } else {
           alert( data.message ) ;
        }
  });
}