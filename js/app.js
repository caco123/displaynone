$(document).ready(function () {
    getHTTPMethod();
    requesPerMinutes();
    responseCode();
});

/****************************************************** funciones  **************************************/

function getHTTPMethod() {
    try {
        $.ajax({
            url: 'api.php',
            data: {
                "accion": 'getRequestMethod'
            },
            type: 'POST',
            success: function (data) {
                //console.log(data);
                var res = JSON.parse(data);
                MethodCanvas(res);
            }
        });
    } catch (err) {
        alert("Ocurrio un error");
        console.log(err);
    }
}
function requesPerMinutes() {
    try {
        $.ajax({
            url: 'api.php',
            data: {
                "accion": 'requesPerMinutes'
            },
            type: 'POST',
            success: function (data) {
                //console.log(data);
                //var res = JSON.parse(data);
                PerMinutesCanvas(data);
            }
        });
    } catch (err) {
        alert("Ocurrio un error");
        console.log(err);
    }
}

function responseCode() {
    try {
        $.ajax({
            url: 'api.php',
            data: {
                "accion": 'responseCode'
            },
            type: 'POST',
            success: function (data) {
                //console.log(data);
                var res = JSON.parse(data);
                responseCodeCanvas(res);
            }
        });
    } catch (err) {
        alert("Ocurrio un error");
        console.log(err);
    }
}

/****************************************************** funciones canvas **************************************/
function MethodCanvas(args) {
    var method = document.getElementById('methodChart').getContext('2d');
    var chart = new Chart(method, {
        // The type of chart we want to create
        type: 'pie',

        // The data for our dataset
        data: {
            labels: ['GET', 'POST', 'HEAD', "INVALID"],
            datasets: [{
                label: 'My First dataset',
                backgroundColor: [
                    'orange',
                    'blue',
                    'green',
                    'red'],
                /*  borderColor: [
                     'rgba(255, 99, 132, 1)',
                     'rgba(54, 162, 235, 1)',
                     'rgba(255, 206, 86, 1)'], */
                data: [args.get.cuenta, args.post.cuenta, args.head.cuenta, args.invalid.cuenta]
            }]
        },

        // Configuration options go here
        options: {}
    });
}
function PerMinutesCanvas(arg) {
    var perminute = document.getElementById('requestsPerMinuteChart').getContext('2d');
    var chart = new Chart(perminute, {
        // The type of chart we want to create
        type: 'bar',
        // The data for our dataset
        data: {
            datasets: [{
                // barPercentage: 0.5,
                //barThickness: 6,
                //maxBarThickness: 8,
                //minBarLength: 2,
                data: [arg]
            }]
        },

        // Configuration options go here
        options: {}
    });
}
function responseCodeCanvas(args) {
    var method = document.getElementById('responseCodeChart').getContext('2d');
    var chart = new Chart(method, {
        // The type of chart we want to create
        type: 'pie',

        // The data for our dataset
        data: {
            labels: args.arreglo_tipo_codigos,
            datasets: [{
                label: 'My First dataset',
                backgroundColor: args.colores,
                /*  borderColor: [
                     'rgba(255, 99, 132, 1)',
                     'rgba(54, 162, 235, 1)',
                     'rgba(255, 206, 86, 1)'], */
                data: args.arreglo_cantidades
            }]
        },

        // Configuration options go here
        options: {}
    });
}