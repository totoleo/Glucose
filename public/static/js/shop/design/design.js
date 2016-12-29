$(function() {
    var module_mouse_down = false,
        module_current = null,
        module_enter = null,
        module_enter_parent = null,
        module_position = false, // true:上 false:下
        module_tmp = $('.module_tmp'),
        module_click_x = -5,
        module_click_y = -5;
    $('.left .module').on('mousedown', function() {
        module_mouse_down = true;
        module_current = $(this);
        module_tmp.text(module_current.children('span').text());
    });
    $(document).on('mousemove', function(e) {
        if (module_mouse_down) {
            // 移动module位置
            module_tmp.css('left', e.clientX - module_click_x).css('top', e.clientY - module_click_y);
            module_tmp.show();
        }
    }).on('mouseup', function(e) {
        if (module_current && module_enter) {
            var code = module_current.data('code'),
                type = module_current.data('type').toString().split(','),
                parent = module_enter_parent.data('type').toString();
            /*$.ajax({
                url: './modules/' + code + '.htm',
                type: 'GET',
                dataType: 'html',
                success: function(h){
                    alert(h);
                },
                error: function(){
                    alert('找不到模块');
                }
            });*/
            if (type.indexOf(parent) == -1) {
                $('.tips').remove();
                module_enter.prepend('<div class="tips">宽度不符</div>');
            } else {
                // 可以放
                if (module_enter.hasClass('module-default')) {
                    module_enter.hide();
                }
                if (module_position) {
                    module_enter.before('<div class="module">' + code + '</div>');
                } else {
                    module_enter.after('<div class="module">' + code + '</div>');
                }
            }
            module_current = null;
        }
        module_mouse_down = false;
        module_tmp.hide();
    }).delegate('div', 'selectstart', function() {
        return false;
    });
    $('.right').delegate('.module,.module-default', 'mouseenter', function() {
        module_enter = $(this);
        module_enter_parent = module_enter.parent();
        $(this).addClass('hover');
    }).delegate('.module,.module-default', 'mouseout', function() {
        module_enter = null;
        $(this).removeClass('hover');
    }).delegate('.module', 'mousemove', function(e) {
        if (module_mouse_down) {
            module_position = ($(this).height() / 2 + 2) > e.offsetY;
        }
    });
});