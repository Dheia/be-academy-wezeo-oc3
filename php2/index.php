
<style>
    .records { grid-area: records; }
    .print_r { grid-area: print_r; }
    .arrivals { grid-area: arrivals; }

    .grid-container {
        display: grid;
        grid-template-areas:
            'records print_r'
            'records arrivals';
        gap: 30px;
        padding: 10px;
        }
</style>
<html>
<body>

    <h1>Logger 2</h1>

    <form method="post" action="index.php">
        
        <label for="name_input_field">Name: </label>
        <input type="text" name="name_field" id="name_input_field" pattern="[A-Za-z0-9 ]{1,}" title="At least 1 character" required="required">
        <label for="msg_field">   Message: </label>
        <input type="text" name="msg_field" id="msg_field" pattern="[a-z]*[A-Z]*[\ .]*" title="Only letters, numbers, spaces and dots">
        <input type="submit" value="submit" name="clockin">

    </form>

</body>
<html>

<?php
    require "logic.php";
?>