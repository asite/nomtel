function createFiltersForm(id,token) {
    $('body').append('<form id="filtersForm" method="get"></form>');
    $('#filtersForm').append('<input type="hidden" name="exportGridView" value="'+id+'">');
    $('#filtersForm').append('<input type="hidden" name="YII_CSRF_TOKEN" value="'+token+'">');
    $('#'+id).find('select, input').each(function(){
        $('#filtersForm').append('<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'">');
    })
    $('#filtersForm').submit();
    $('#filtersForm').remove();
    return false;
}