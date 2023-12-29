<?php

class MasterModelloFondoCostituzioneFooter
{
    public static function render_scripts()
    {
?>
<script>
    function ExportExcel(index) {
        let worksheet_tmp1, a, sectionTable;
        let temp = [''];
        for (let i = 0; i < index; i++) {
            sectionTable = document.getElementById('exportable_table' + i);
            worksheet_tmp1 = XLSX.utils.table_to_sheet(sectionTable);
            a = XLSX.utils.sheet_to_json(worksheet_tmp1, {header: 1})
            temp = temp.concat(['']).concat(a)
        }

        let worksheet = XLSX.utils.json_to_sheet(temp, {skipHeader: true})

        const new_workbook = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(new_workbook, worksheet, "worksheet")
        XLSX.writeFile(new_workbook, ('xlsx' + 'Dasein1.xlsx'))
    }
</script>
<?php
    }

    public static function render()
    {
        self::render_scripts();
    }
}