
$(document).ready(function()
{
    $.url = function(url)
    {
        var pathArray = window.location.pathname.split('/');
        return pathArray[2] + url;
    };

    var taskTableData = new Array(new Array());
    var quantityOfMachines = 2;

    $('#taskTable').handsontable({
        startCols: 2,
        startRows: 1,
        colHeaders: ["Machine 1", "Machine 2"],
        rowHeaders: true
    });


    $('#btnAdd').click(function()
    {
        $('#taskTable').handsontable('alter', 'insert_row', $('#taskTable').handsontable('countRows'));
    });

    $('#btnDel').click(function()
    {
        $('#taskTable').handsontable('alter', 'remove_row', $('#taskTable').handsontable('countRows') - 1);
    });

    $('#solveJohnson').click(function()
    {
        taskTableData = $('#taskTable').handsontable('getData');
        var isValid = ValidateInput(taskTableData);

        if (!isValid)
        {
            $('#warning').html('Please insert numeric values only.');
            $('#modal').modal('show');
            return false;
        }

        if (quantityOfMachines === 3)
        {
            var isBottleNeck = CheckBottleNeck(taskTableData);
            switch (isBottleNeck)
            {
                case 1:
                    $('#warning').html('Machine 2 must not dominate over machine 1.');
                    $('#modal').modal('show');
                    return false;
                case 3:
                    $('#warning').html('Machine 2 must not dominate over machine 3.');
                    $('#modal').modal('show');
                    return false;
                case 0:
                    break;
                default:
                    return false;
            }
        }

        var incomingURL = window.location.pathname;

        $.post(incomingURL, {tableData: taskTableData, machines: quantityOfMachines}, function(data)
        {
            $('#gant').html(data.gantti);
            $('#totalTime').html(data.totalTime);
            $('#order').html(data.order);
        });
    });


    $('#rand').click(function()
    {
        var numberOfTasks = $('#taskTable').handsontable('countRows');

        for (var i = 0; i < numberOfTasks; i++)
        {
            if (quantityOfMachines === 3)
            {
                //Uwzglęgnienie bottle-neck - losowanie tylko prawidłowych czasów
                var timeOnM1 = Math.floor((Math.random() * 10) + 1);
                var timeOnM3 = Math.floor((Math.random() * 10) + 1);
                var ceilling = Math.min(timeOnM1, timeOnM3) - 1;
                var timeOnM2 = Math.floor((Math.random() * ceilling) + 1);
                taskTableData[i] = [timeOnM1, timeOnM2, timeOnM3];
            }

            if (quantityOfMachines === 2)
                taskTableData[i] = [Math.floor((Math.random() * 10) + 1), Math.floor((Math.random() * 10) + 1)];
        }
        $('#taskTable').handsontable('loadData', taskTableData);
    });


    $('#switch').click(function()
    {
        if ($('#switch').html() === "M3")
        {
            $('#switch').html("M2");
            quantityOfMachines = 3;
            $('#taskTable').handsontable('updateSettings', {colHeaders: ["Machine 1", "Machine 2", "Machine 3"], startCols: 3, rowHeaders: true});
        }
        else
        {
            $('#switch').html("M3");
            quantityOfMachines = 2;

            $('#taskTable').handsontable('updateSettings', {colHeaders: ["Machine 1", "Machine 2"], startCols: 2, rowHeaders: true});
            $('#taskTable').handsontable('alter', 'remove_col', 2);
        }
    });

    function ValidateInput(data)
    {
        for (var i = 0; i < data.length; i++)
        {
            for (var j = 0; j < data[i].length; j++)
            {
                var pattern = /[^0-9]/i;
                var value = data[i][j];
                if (value === null || value === "" || pattern.test(value))
                    return false;
            }
        }
        return true;
    }

    function CheckBottleNeck(data)
    {
        var onM1 = new Array();
        var onM2 = new Array();
        var onM3 = new Array();

        for (var i = 0; i < data.length; i++)
        {
            onM1.push(data[i][0]);//taskTableData.
            onM2.push(data[i][1]);
            onM3.push(data[i][2]);
        }
        var minOnM1 = Math.min.apply(Math, onM1);
        var minOnM3 = Math.min.apply(Math, onM3);
        var maxOnM2 = Math.max.apply(Math, onM2);

        if (!(minOnM1 >= maxOnM2 || minOnM3 >= maxOnM2))
        {
            if (!(minOnM1 >= maxOnM2))
                return 1;

            if (!(minOnM3 >= maxOnM2))
                return 3;
        }


        return 0;
    }
});
