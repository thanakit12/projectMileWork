
    $(document).ready(function () {
        $('.ty-phone').keypress(function (e) {
            if (e.which !== 8 && e.which !== 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
    });

    function validate(field) {
        let phone;
        const length = field.value.length;
        if (length === 10) {
            phone = field.value.replace(/(\d\d\d)(\d\d\d)(\d\d\d\d)/, "$1-$2-$3");
        } else if (length === 9) {
            phone = field.value.replace(/(\d\d)(\d\d\d)(\d\d\d\d)/, "$1-$2-$3");
        } else {
            window.alert("กรุณากรอกข้อมูล โทรศัพท์ให้ถูกต้อง")
            phone = '';
        }
        field.value = phone;
    }
