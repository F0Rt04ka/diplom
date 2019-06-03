function getColumnsData(blockId)
{
    let columnsDataField = $('#' + blockId + '-columns');
    if (!columnsDataField.val()) {
        columnsDataField.val('[]');
    }

    return JSON.parse(columnsDataField.val());
}

function setColumnsData(blockId, data)
{
    let columnsDataField = $('#' + blockId + '-columns');
    columnsDataField.val(JSON.stringify(data));
}

function insertCellInTable(blockId, cellType, rowNum, colNum)
{
    let $currentRow = $($('#' + blockId + '-content table tbody tr')[rowNum]);
    let newCell = document.createElement('td');
    newCell.innerHTML = createCellByType(blockId, cellType, rowNum, colNum);
    $currentRow.append(newCell);
}

function createCellByType(blockId, cellType, rowNum, colNum)
{
    return $('#' + blockId)
        .data('prototypeCell_' + cellType)
        .replace(/%row_num%/g, rowNum)
        .replace(/%col_num%/g, colNum);
}

function insertNewColumnHeader(blockId, colName, colNum)
{
    let $block = $('#'+blockId);
    let rows = $block.find('table thead tr');
    let colEditBtnBlock = $block.data('prototypeColEditBtn').replace(/%col_num%/g, colNum);
    let colNameBlock = $block.data('prototypeColName').replace(/%col_num%/g, colNum).replace(/%value%/g, colName);
    rows.filter(':first').append(colEditBtnBlock);
    rows.filter(':last').append(colNameBlock);
}

window.handleTableAddRowBtn = function (btn)
{
    let $btn = $(btn);
    let blockId = $btn.data('blockId');
    let $tableBody = $('#' + blockId + '-content table tbody');
    let columnsData = getColumnsData(blockId);
    if (!columnsData) {
        return;
    }

    let rowNum = $tableBody.find('tr').length;

    $tableBody.append(document.createElement('tr'));

    columnsData.forEach(function (column, index) {
        insertCellInTable(blockId, column.text_size, rowNum, index);
        addHandlerOnCell(blockId, rowNum, index);
    });
    handleCellChange(blockId);
};

function handleCellChange(blockId) {
    let inputFields = $('#'+blockId+' table tbody').find('input, textarea');
    let cellsData = [];
    if (!inputFields.length) {
        return;
    }

    inputFields.each(function (i, field) {
        let $field = $(field);
        let rowNum = $field.data('rowNum');
        if (cellsData[rowNum] === undefined) {
            cellsData[rowNum] = [];
        }
        cellsData[rowNum][$field.data('colNum')] = $field.val().toString();
    });

    $('#'+blockId+'-cells').val(JSON.stringify(cellsData))
}

function addHandlerOnCell(blockId, rowNum, colNum)
{
    $('#'+blockId+' [data-row-num='+rowNum+'][data-col-num='+colNum+']').on('change', function () {
        handleCellChange(blockId);
    })
}

function getDataFromEditColumnForm()
{
    return  {
        'name': $('#table_block-modal_add_column_field_column_name').val(),
        'text_orientation': $('#table_block-modal_add_column_field_text_orientation').val(),
        'text_size': $('#table_block-modal_add_column_field_text_size').val(),
        'text_align': $('#table_block-modal_add_column_field_text_align').val(),
        'width': $('#table_block-modal_add_column_field_width').val()
    };
}

function getHiddenDataFromForm()
{
    let data = {};
    $('[id^=table_block-modal_column_hidden_]').each(function (i, field) {
        data[$(field).attr('name')] = $(field).val();
    });

    return data;
}

function setDataToEditColumnForm(data, hiddenData)
{
    $('#table_block-modal_add_column_field_column_name').val(data.name);
    $('#table_block-modal_add_column_field_text_orientation').val(data.text_orientation);
    $('#table_block-modal_add_column_field_text_size').val(data.text_size);
    $('#table_block-modal_add_column_field_text_align').val(data.text_align);
    $('#table_block-modal_add_column_field_width').val(data.width);

    if (hiddenData) {
        $.each(hiddenData, function (key, val) {
            $('#table_block-modal_column_hidden_' + key).val(val);
        });
    }
}

function initModalForm()
{
    $('#table_block-modal_add_column .column_edit').hide();
    $('#table_block-modal_add_column .column_add').hide();
    $('[id^=table_block-modal_column_hidden_]').val('');
    $('input[id^=table_block-modal_add_column]').val('');
}

$(document).ready(function () {
    initModalForm();

    $('#table_block-modal_add_column')
        .on('show.bs.modal', function (event) {
            let $btn = $(event.relatedTarget);
            let blockId = $btn.data('blockId');

            if ($btn.data('colNum') === undefined) {
                // Create new column
                $('#table_block-modal_column_hidden_blockId').val(blockId);
                $(this).find('.column_add').show();
                return;
            }

            let columnsData = getColumnsData(blockId);
            let colNum = $btn.data('colNum');
            setDataToEditColumnForm(columnsData[colNum], {'blockId': blockId, 'colNum': colNum});
            $(this).find('.column_edit').show();
        })
        .on('hidden.bs.modal', initModalForm);

    $('#table_block-modal_btn_column_add').on('click', function () {
        let blockId = getHiddenDataFromForm().blockId;
        let data = getDataFromEditColumnForm();
        let columnsData = getColumnsData(blockId);
        columnsData.push(data);
        setColumnsData(blockId, columnsData);

        insertNewColumnHeader(blockId, data.name, columnsData.length - 1);

        let colNum = columnsData.length - 1;
        for (let i = 0; i < $('#' + blockId + ' table tbody tr').length; i++) {
            insertCellInTable(blockId, data.text_size, i, colNum);
            addHandlerOnCell(blockId, i, colNum);
        }
        handleCellChange(blockId);


        $('#table_block-modal_add_column').modal('hide');
    });

    $('#table_block-modal_btn_column_edit').on('click', function () {
        let hiddenData = getHiddenDataFromForm();
        let columnsData = getColumnsData(hiddenData.blockId);
        let data = getDataFromEditColumnForm();
        let $block = $('#'+hiddenData.blockId);
        columnsData[hiddenData.colNum] = data;
        setColumnsData(hiddenData.blockId, columnsData);

        $block.find('th[data-col-num=' + hiddenData.colNum + ']').text(data.name);
        $block.find('tbody td [data-col-num='+hiddenData.colNum+']').each(function (i, elem) {
            let val = $(elem).val();
            let $parentNode = $(elem).parent();
            $parentNode.html(createCellByType(
                hiddenData.blockId, data.text_size, $(elem).data('rowNum'), $(elem).data('colNum')
            ));
            $parentNode.children(':first').val(val);
        });


        $('#table_block-modal_add_column').modal('hide');
    });

    $('#table_block-modal_btn_column_delete').on('click', function () {
        let hiddenData = getHiddenDataFromForm();
        let columnsData = getColumnsData(hiddenData.blockId);
        let $block = $('#'+hiddenData.blockId);

        $block.find('thead [data-col-num='+hiddenData.colNum+']').remove();
        $block.find('tbody [data-col-num='+hiddenData.colNum+']').each(function (i, elem) {
            $(elem).parent().remove();
        });

        for (let i = parseInt(hiddenData.colNum); i < columnsData.length; i++) {
            $block.find('[data-col-num='+i+']').each(function (a, elem) {
                $(elem).data('colNum', i - 1);
                $(elem).attr('data-col-num', i - 1);
            })
        }

        columnsData.splice(hiddenData.colNum, 1);
        setColumnsData(hiddenData.blockId, columnsData);


        $('#table_block-modal_add_column').modal('hide');
    });
});