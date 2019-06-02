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
    let $btn = $('#' + blockId + ' .table_block-btn_add_row');
    let $currentRow = $($('#' + blockId + '-content table tbody tr')[rowNum]);
    let newCell = $btn.data('prototypeCell_' + cellType)
        .replace(/%row_num%/, rowNum)
        .replace(/%col_num%/, colNum);
    $currentRow.append(newCell);
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


$(document).ready(function () {
    $('#table_block-modal_add_column')
        .on('show.bs.modal', function (event) {
            let btn = $(event.relatedTarget);

            $('#table_block-modal_add_column_data_blockId').val(btn.data('blockId'));
            $('#table_block-modal_add_column_data_blockName').val(btn.data('blockName'));
        })
        .on('hidden.bs.modal', function () {
            $('input[id^=table_block-modal_add_column]').val('');
        });

    $('#table_block-modal_btn_add_column').on('click', function () {
        let blockId = $('#table_block-modal_add_column_data_blockId').val();
        let table = $('#' + blockId + '-content table');

        let newColumn = {
            'name': $('#table_block-modal_add_column_field_column_name').val(),
            'text_orientation': $('#table_block-modal_add_column_field_text_orientation').val(),
            'text_size': $('#table_block-modal_add_column_field_text_size').val(),
            'text_align': $('#table_block-modal_add_column_field_text_align').val(),
            'width': $('#table_block-modal_add_column_field_width').val()
        };

        let newHeaderElem = document.createElement('th');
        newHeaderElem.textContent = newColumn.name;
        table.find('thead tr').append(newHeaderElem);

        let columnsData = getColumnsData(blockId);
        columnsData.push(newColumn);
        setColumnsData(blockId, columnsData);

        let colNum = columnsData.length - 1;
        for (let i = 0; i < $('#' + blockId + ' table tbody tr').length; i++) {
            insertCellInTable(blockId, newColumn.text_size, i, colNum);
            addHandlerOnCell(blockId, i, colNum);
        }
        handleCellChange(blockId);


        $('#table_block-modal_add_column').modal('hide');
    });
});