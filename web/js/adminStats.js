//Utility function render
var rankingFutilidadStats = (new Function("return [" + rankingFutilidadStats + "];")());

//Alu roja Render
var rankingAluRojaStats = (new Function("return [" + rankingAluRojaStats + "];")());

//Alu roja Render
var rankingAluBlancaStats = (new Function("return [" + rankingAluBlancaStats + "];")());

//lines Render
barChart = (new Function("return {"+ barChart +"};")());

//lines Render
var lineChart = (new Function("return {"+ lineChart +"};")());

window.onload = function () {
    var ctx = document.getElementById("chart-area1").getContext("2d");
    window.myPie = new Chart(ctx).Pie(rankingFutilidadStats);

    ctx = document.getElementById("chart-area2").getContext("2d");
    window.myPie = new Chart(ctx).Pie(rankingAluRojaStats);

    ctx = document.getElementById("chart-area3").getContext("2d");
    window.myPie = new Chart(ctx).Pie(rankingAluBlancaStats);

    ctx = document.getElementById("chart-area4").getContext("2d");
    window.myLine = new Chart(ctx).Line(lineChart, {
        responsive: true
    });

    ctx = document.getElementById("chart-area5").getContext("2d");
    window.myBar = new Chart(ctx).Bar(barChart, {
        responsive : true
    });
};
