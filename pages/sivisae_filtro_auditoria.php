<?php
if (isset($pf) && $pf !== '2') {
    ?>
    <td class="aud">Auditor
        <select data-placeholder="Seleccione un Auditor" class="chosen-select-deselect" name='auditor' id='auditor' style="width: 200px;" tabindex='2'>";
            <option value=''></option>
            <?php
            $auditor_c = $consulta->auditores();
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
} else if ($pf === '2') {
    $auditor = mysql_fetch_array($consulta->traerAuditor(null, $_SESSION['usuarioid']));
    $aud_id = $auditor[0];
    $aud_nombre = $auditor[1];
    ?>
    <input type="hidden" name="auditor" id="auditor" value="<?php echo $aud_id ?>"/>
    <?php
}
?>