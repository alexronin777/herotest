function fight() {
    $.post("/hero/public/index.php?action=fight",
        {
            fighter1: $("#heroForm").serialize(),
            fighter2: $("#beastForm").serialize()
        },
        function (data, status) {
            //alert("Data: " + data + "\nStatus: " + status);
        });
}

function randomizeFieldValues(form) {
    let inputs = $('#' + form + ' :input[type=number]');
    if (inputs.length > 0) {
        for (i = 0; i < inputs.length; i++) {
            $(inputs[i]).val(getRandomNumberInRange($(inputs[i]).attr('min'), $(inputs[i]).attr('max')));
        }
    }

}

function getRandomNumberInRange(min, max) {
    min = parseInt(min);
    max = parseInt(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}