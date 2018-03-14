<?php
if (isset($pf) && $pf !== '5' && $pf !== '9') {
    ?>
    <td class="aud">Monitor
        <select data-placeholder="Seleccione un Monitor" class="chosen-select-deselect" name='auditor' id='auditor' style="width: 200px;" tabindex='2'>";
            <option value=''></option>
            <?php
            $auditor_c = $consulta->monitores();
            while ($row1 = mysql_fetch_array($auditor_c)) {
                $aud_id = $row1[0];
                $aud_nombre = ucwords(strtolower($row1[1]));
                $gen = ucwords(strtolower($row1[2]));
                echo "<option value='$aud_id'>";
                echo $aud_nombre . " - " . $gen;
                echo "</option>";
            }
            ?>
        </select>
    </td>
    <?php
} else if ($pf === '9') {
    $auditor = mysql_fetch_array($consulta->traerMonitor(null, $_SESSION['usuarioid']));
    $aud_id = $auditor[0];
    $aud_nombre = $auditor[1];
    ?>
    <input type="hidden" name="auditor" id="auditor" value="<?php echo $aud_id ?>"/>
    <?php
}
?>