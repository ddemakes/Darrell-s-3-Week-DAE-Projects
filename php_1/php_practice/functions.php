<?php
/**
 * getClinics()
 * Returns an array of all clinics (ordered A-Z).
 *
 * @param mysqli $conn  An open MySQLi connection.
 * @return array        Each element = ['id' => int, 'clinicname' => string]
 */
function getClinics(mysqli $conn): array
{
    $clinics = [];
    $sql = "SELECT id, clinicname FROM Clinics ORDER BY clinicname ASC";
    if ($result = mysqli_query($conn, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $clinics[] = $row;
        }
        mysqli_free_result($result);
    }
    return $clinics;
}

/**
 * renderClinicOptions()
 * Helper to turn the array above into <option> tags.
 *
 * @param array $clinics  Output of getClinics()
 * @return string         HTML string of <option>…</option>
 */
function renderClinicOptions(array $clinics): string
{
    $html = '<option value="">-- Choose a Clinic --</option>';
    foreach ($clinics as $c) {
        $id   = htmlspecialchars($c['id']);
        $name = htmlspecialchars($c['clinicname']);
        $html .= "<option value=\"$id\">$name</option>";
    }
    return $html;
}
// echo "hello world";?>
