//lines Render
var lineChart = (new Function("return {"+ lineChart +"};")());

window.onload = function () {
    var ctx = document.getElementById("chart-area").getContext("2d");
    window.myLine = new Chart(ctx).Line(lineChart);

};
