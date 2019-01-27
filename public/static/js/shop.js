$.extend({
    StandardPost:function(url,args){
        var form = $("<form method='post' action='"+url+"'></form>"),input;
        $.each(args,function(key,value){
            input = $("<input type='hidden' />");
            input.attr("name" , key);
            input.val(value);
            form.append(input);
        });
        $(document.body).append(form);
        form.submit();
    }
});