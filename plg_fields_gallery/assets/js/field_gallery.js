var fieldGallery = {
    initialized: false,
    init: function () {
        if (this.initialized) {
            return;
        }
        jQuery("tbody.sortable").sortable();
    },
    getRow: function (name, id, image) {
        return '\t\t\t\t\t<tr>\n' +
            '\t\t\t\t\t\t<td class="center vcenter"><span style="cursor: move;" class="sortable-handler"><span class="icon-menu"></span></span></td>\n' +
            '\t\t\t\t\t\t<td>\n' +

            '\t\t<div class="input-prepend input-append">\n' +
            '\t\t<span rel="popover" class="add-on pop-helper field-media-preview" title="" data-content="' + plg_fieldGallery_notImage + '" data-original-title="' + plg_fieldGallery_selImage + '" data-trigger="hover">\n' +
            '\t\t\t<span class="icon-eye" aria-hidden="true"></span>\n' +
            '\t\t</span>\n' +
            '\t\t\t<input name="' + name + '[image]" id="' + id + '_image" value="'+image+'" readonly="readonly" class="input-small hasTooltip field-media-input" data-original-title="" title="" type="text">\n' +
            '<a class="modal btn add-on button-select" ' +
            'href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_content&amp;author=&amp;fieldid=' + id + '_image&amp;folder=" ' +
            'rel="{handler: \'iframe\', size: {x: 1000, y: 600}}">' + plg_fieldGallery_selectButton + '</a>' +
            '\t\t\t<a class="btn icon-remove hasTooltip add-on button-clear" title="" onclick="jQuery(\'#' + id + '_image\').val(\'\');" data-original-title="' + plg_fieldGallery_clearButton + '"></a>\n' +
            '\t</div>\n' +
            '</td>\n' +
            '\t\t\t\t\t\t<td><input class="input-xlarge title span12" name="' + name + '[title]" value="" aria-invalid="false" type="text"></td>\n' +
            '\t\t\t\t\t\t<td><input class="input-xlarge span12" name="' + name + '[desc]" value="" type="text"></td>\n' +
            '\t\t\t\t\t\t<td><input class="input-xlarge span12" name="' + name + '[attr]" value="" type="text"></td>\n' +
            '\t\t\t\t\t\t<td class="center"><input class="btn btn-small btn-danger" onclick="fieldGallery.deleteRow(this)" value="–" type="button"></td>\n' +
            '\t\t\t\t\t</tr>'
    },
    deleteRow: function (element) {
        jQuery(element).parents('tr').remove();
    },
    deleteAll: function (element) {
        if(confirm(plg_fieldGallery_deleteAll)){
            jQuery(element).parents('.gallery-div').find('tbody.sortable').html('');
        }
    },
    addRow: function (element, name, id) {
        var table = jQuery(element).parents('table').find('tbody');
        var key = 0;
        while (jQuery('#' + id + '_' + key + '_image').length > 0) {
            key++;
        }
        id = id + '_' + key;
        name = name + '[' + key + ']';
        var row = this.getRow(name, id, '');
        table.append(row).sortable();

        SqueezeBox.initialize({});
        SqueezeBox.assign(jQuery('a.modal', table).get(), {
            parse: 'rel'
        });
        jQuery('a.modal', table).removeClass('modal');
    },
    sortRows: function (element) {
        var $element, tbody, direction, rowsArray, compare;

        $element = jQuery(element);
        if ($element.hasClass('direction-desc')) {
            direction = 'desc';
            $element.addClass('direction-asc').removeClass('direction-desc');
        }
        else if ($element.hasClass('direction-asc')) {
            direction = 'asc';
            $element.addClass('direction-desc').removeClass('direction-asc');
        }
        else {
            direction = 'asc';
            $element.addClass('direction-desc');
        }

        tbody = $element.parents('.gallery-div').find('tbody.sortable');

        // make array from TR
        rowsArray = jQuery(tbody).find('tr');

        compare = function (rowA, rowB) {
            var rowAval, rowBval;
            rowAval = jQuery(rowA).find('input.title').val();
            rowBval = jQuery(rowB).find('input.title').val();
            if (direction == 'asc') {
                return rowAval > rowBval ? 1 : -1;
            }
            else {
                return rowAval < rowBval ? 1 : -1;
            }

        };

        // sort
        rowsArray.sort(compare);

        for (var i = 0; i < rowsArray.length; i++) {
            tbody.append(rowsArray[i]);
        }

        return false;
    },
    fill: function (element, name, id) {
        var $element = jQuery(element);
        var parent = $element.parents('.gallery-div');
        var dir = parent.find('.dir-select').val();
        var $this = this;
        if(dir == ''){
            alert('Please select folder');
            return false;
        }
        var mainDir = parent.find('.main-dir').val();
        if(mainDir != ''){
            dir = mainDir+'/'+dir;
        }
        jQuery.ajax({
            url: '/administrator/index.php?option=com_media&task=ajax.get_folder_images',
            dataType: 'json',
            type:     'POST',
            data:     {dir: dir},
            success: function (data, status, jqXHR)
            {
                if (status == 'success')
                {
                    if(data.error == 1){
                        alert(data.message);
                        return false;
                    }
                    else{
                        var row;
                        var table = parent.find('tbody.sortable');
                        var key = 0;
                        var curName, curId;
                        jQuery.each(data.data,function(index,value){
                            while (jQuery('#' + id + '_' + key + '_image').length > 0) {
                                key++;
                            }
                            curId = id + '_' + key;
                            curName = name + '[' + key + ']';
                            row = $this.getRow(curName, curId, dir+'/'+value.toString());
                            table.append(row).sortable();
                        });

                        SqueezeBox.initialize({});
                        SqueezeBox.assign(jQuery('a.modal', table).get(), {
                            parse: 'rel'
                        });
                        jQuery('a.modal', table).removeClass('modal');
                    }

                }
                else{
                    alert('Inretnal server error');
                }

            }
        });
    }
};

jQuery(document).ready(function () {
    fieldGallery.init();
});