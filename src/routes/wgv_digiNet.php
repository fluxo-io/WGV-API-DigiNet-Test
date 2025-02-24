<?php
// Laden der Konfiguration
$config = require APPROOT . '/config/fluxo.php';

// API-URL aus der Konfiguration
$api_url = $config['api']['url'];
$ssl_verify = $config['api']['ssl_verify'];

// Eingehende Daten lesen und als xml pharsen
$input_data = file_get_contents('php://input');
$xml = simplexml_load_string($input_data);

// error_log("External API response (HTTP Code $api_http_code): " . $api_response);
http_response_code(response_code: 200);

if ($xml === false) {
    $response = [
        "messages" => [
            [
                "type" => "error",
                "message" => "Invalid XML format."
            ]
        ],
        "patientId" => null,
        "saved" => false
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Antwort generieren
$messages = [];
$messages[] = 'Diese Antwort wurde von der WGV-API generiert.';
$messages[] = '---------------------';

// keine Fehler bei Start
$no_error = true;

// xml: visiten durchlaufen und testen
foreach ($xml->{"medical-data"}->{"digiNetPlannedVisit"} as $visit) {
    if (empty($visit->{"diginet-visit-id"}) || $visit->{"diginet-visit-id"} == '-NA-') {
        $messages = [];
        $messages[] = 'Diese Antwort wurde von der WGV-API generiert.';
        $messages[] = '---------------------';
        $messages[] = 'Bitte ergänzen Sie die Visiten-ID. Weitere Felder werden nach Korrektur geprüft.';
        $no_error = false;
        break;
    } else {
        if (!isset($visit->{"digiNetTherapy"})) {
            $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] 3-Therapie-A bis E fehlt';
            $no_error = false;
        } else {
            if (empty($visit->{"visit-data"}->{"digiNetPlannedVisitPatientTransfer"}) || $visit->{"visit-data"}->{"digiNetPlannedVisitPatientTransfer"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Antwort zum Patiententransfer fehlt unter "3-Therapie-A bis E"';
                $no_error = false;
            }
            if (empty($visit->{"visit-data"}->{"digiNetPlannedVisitLostToFollowUp"}) || $visit->{"visit-data"}->{"digiNetPlannedVisitLostToFollowUp"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Antwort zu Lost-to-Follow up fehlt unter "3-Therapie-A bis E"';
                $no_error = false;
            }
            if (empty($visit->{"visit-data"}->{"digiNetPlannedVisitDate"}) || $visit->{"visit-data"}->{"digiNetPlannedVisitDate"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Datum der Visite fehlt unter "3-Therapie-A bis E"';
                $no_error = false;
            } elseif ($visit->{"visit-data"}->{"digiNetPlannedVisitDate"} > "2025-04-01") {
                $messages[] = "[Visite: {$visit->{"diginet-visit-id"}}] Datum der Visite darf nicht nach 01.04.2025 sein";
                $no_error = false;
            }
            if (empty($visit->{"visit-data"}->{"digiNetPlannedVisitEvent"}) || $visit->{"visit-data"}->{"digiNetPlannedVisitEvent"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Eventzyklus fehlt unter "3-Therapie-A bis E"';
                $no_error = false;
            }
        }

        if (!isset($visit->{"digiNetTherapyResponse"})) {
            $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] 3-Therapieansprechen fehlt';
            $no_error = false;
        } else {
            if (empty($visit->{"digiNetTherapyResponse"}->{"therapyAssessmentDate"}) || $visit->{"digiNetTherapyResponse"}->{"therapyAssessmentDate"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Datum der Einschätzung des Therapieansprechens fehlt unter "3-Therapieansp."';
                $no_error = false;
            }
            if (empty($visit->{"digiNetTherapyResponse"}->{"therapyAssessment"}) || $visit->{"digiNetTherapyResponse"}->{"therapyAssessment"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Einschätzung des Therapieansprechens fehlt unter "3-Therapieansp."';
                $no_error = false;
            }
            if (empty($visit->{"digiNetTherapyResponse"}->{"therapyAssessmentBasis"}) || $visit->{"digiNetTherapyResponse"}->{"therapyAssessmentBasis"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Basis der Einschätzung fehlt unter "3-Therapieansp."';
                $no_error = false;
            }
            if (empty($visit->{"digiNetTherapyResponse"}->{"secondMalignancySinceLastDigiNetVisit"}) || $visit->{"digiNetTherapyResponse"}->{"secondMalignancySinceLastDigiNetVisit"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Antwort zum Zweitmalignom fehlt unter "3-Therapieansp."';
                $no_error = false;
            }
            if (empty($visit->{"digiNetHospitalization"}->{"hospitalizationSinceLastDigiNetVisit"}) || $visit->{"digiNetHospitalization"}->{"hospitalizationSinceLastDigiNetVisit"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Antwort zum Krankenhausaufenthalt fehlt unter "3-Therapieansp."';
                $no_error = false;
            }
        }
        
        if (!isset($visit->{"digiNetEcog"})) {
            $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] 3-ECOG fehlt';
            $no_error = false;
        } else {
            if (empty($visit->{"digiNetEcog"}->{"surveyDate"}) || $visit->{"digiNetEcog"}->{"surveyDate"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Datum fehlt unter "3-ECOG"';
                $no_error = false;
            }
            if (empty($visit->{"digiNetEcog"}->{"ecog"}) || $visit->{"digiNetEcog"}->{"ecog"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] ECOG fehlt unter "3-ECOG"';
                $no_error = false;
            }
        }
        
        if (!isset($visit->{"visit_digiNetVitalStatus"})) {
            $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] 3-Vitalstatus fehlt';
            $no_error = false;
        } else {
            if (empty($visit->{"visit_digiNetVitalStatus"}->{"state"}) || $visit->{"visit_digiNetVitalStatus"}->{"state"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Vitalstatus fehlt unter "3-Vitalstatus"';
                $no_error = false;
            }
    
            if (empty($visit->{"visit_digiNetVitalStatus"}->{"weight"}) || $visit->{"visit_digiNetVitalStatus"}->{"weight"} == 0) {
                unset($visit->{"visit_digiNetVitalStatus"}->{"weight"});
            } elseif (($visit->{"visit_digiNetVitalStatus"}->{"weight"} > 0 && $visit->{"visit_digiNetVitalStatus"}->{"weight"} <= 20)) {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Das Gewicht des Patienten darf nicht kleinergleich 20kg sein."';
                $no_error = false;
            }

            if (empty($visit->{"digiNetEpro"}->{"ePROFilledSinceLastDigiNetVisit"}) || $visit->{"digiNetEpro"}->{"ePROFilledSinceLastDigiNetVisit"} == '-NA-') {
                $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Antwort zu den ePROs fehlt unter "3-Vitalstatus"';
                $no_error = false;
            }
        }

        if (isset($visit->{"digiNetHospitalization"})) {
            if (isset($visit->{"digiNetHospitalization"}->{"hospitalizationDate"})) {
                if ($visit->{"digiNetHospitalization"}->{"hospitalizationSinceLastDigiNetVisit"} == 'no'){
                    $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Widerspruch bei Angaben KH-Aufenthalt, vgl. "3-Therapieansprechen';
                    $no_error = false;
                }
            } else {
                if ($visit->{"digiNetHospitalization"}->{"hospitalizationSinceLastDigiNetVisit"} == 'yes'){
                    $messages[] = '[Visite: '.$visit->{"diginet-visit-id"}.'] Widerspruch bei Angaben KH-Aufenthalt, vgl. "3-Therapieansprechen';
                    $no_error = false;
                }
            }
        }

    }
}


// XML zurück in einen String konvertieren
$input_data = $xml->asXML();

// Daten an api_url weiterleiten, wenn keine Fehler gefunden wurden
if ($no_error) {
    $headers = getallheaders();
    $incoming_api_key = isset($headers['Authorization']) ? $headers['Authorization'] : '';    

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/xml",
        "Content-Length: " . strlen($input_data),
        "Authorization: $incoming_api_key"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input_data);

    // SSL-Verifizierungsoption basierend auf der Konfiguration setzen
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $ssl_verify ? 2 : 0);

    $api_response = curl_exec($ch);
    $response = $api_response;
    $api_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($api_http_code > 299) {
        $messages[] = '(Mainzelliste) Fehler bei indentifizierenden Daten.';
        $no_error = false;
    }

    curl_close($ch);
}

if ($no_error) {
    $messages[] = '---------------------';
    $messages[] = 'Die Daten wurden weiter an das UKK gesendet.';
} else {
    $messages[] = '---------------------';
    $messages[] = 'Die Daten wurden NICHT weiter an das UKK gesendet.';
}

$response = [
    "messages" => array_map(function($message) {
        return [
            "type" => "warning",
            "message" => $message
        ];
    }, $messages),
    "patientId" => "1",
    "saved" => true
];

header('Content-Type: application/json');
echo json_encode($response);