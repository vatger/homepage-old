<?php

return array (
  'begin' => 'Beginn',
  'bookings' => 'ATC Buchungen',
  'delete' => 'Buchung löschen',
  'errors' =>
  array (
    'alreadyBooked' => 'Die Station ist im ausgewählten Zeitraum bereits besetzt.',
    'toFarFuture'  => 'Der ausgewählte Zeitraum liegt zu weit in der Zukunft.',
    'failedVatbook' =>
    array (
      'insert' => 'Buchung lokal gespeichert.
Leider trat bei der Übertragung zu VatBook ein Fehler auf.',
      'update' => 'Buchung lokal aktualisiert.
Leider trat bei der Übertragung zu VatBook ein Fehler auf.',
    ),
    'notController' => 'Du bist nicht der Lotse dieser Session.',
    'notEligable' => 'Du benötigst mindestens ein S1 Rating um eine Buchung vornehmen zu können.',
    'timeframeLimits' => 'Die zeitlichen Beschränkungen einer Buchung wurden nicht eingehalten.
Es muss für mindestens 60 Minuten gebucht werden. Eine Buchung darf einen Zeitraum von 24 Stunden nicht überschreiten.',
    'timeframePast' => 'Die Buchung darf maximal 2 Stunden in der Vergangenheit beginnen.',
    'timeframeSense' => 'Bitte stelle sicher, dass der Beginn zeitlich vor dem geplanten Ende der Buchung liegt.',
  ),
  'filter' => 'Filter',
  'frequency' => 'Frequenz',
  'make' =>
  array (
    'abort' => 'Abbrechen',
    'editMode' => 'ATC Buchung Bearbeiten',
    'guide' => 'Um eine ATC Buchung vorzunehmen oder zu editieren kannst du einen Flugplatz aussuchen und anschließend eine zugeordnete Station auswählen oder aber einfach eine Station suchen.

Die Suche nach einer Station wird die Flugplatzauswahl deaktivieren und automatisch die gesuchte Station selektieren.',
    'newMode' => 'ATC Buchung Vornehmen',
    'save' => 'Speichern',
    'select' =>
    array (
      'aerodrome' => 'Flugplatz Auswählen',
      'begin' => 'Beginn ( Zeit in UTC ):',
      'end' => 'Ende ( Zeit in UTC ):',
      'event' => 'Ist dies eine Buchung zu einem Event?',
      'search' => 'ATC Station Suchen',
      'station' => 'Station Auswählen',
      'training' => 'Ist diese Session ein Training?',
      'voice' => 'Wird Voice verwendet?',
    ),
  ),
  'modify' => 'Buchung bearbeiten',
  'mybookings' => 'Meine ATC Buchungen',
  'next48hours' => 'Stationen, die in den nächsten 48 Stunden gebucht sind.',
  'nobookings' => 'Keine Buchungen Im System!',
  'nobookings_actions' => 'Um dies zu ändern, begib dich zu der ATC Buchungsseite und sichere dir deine Sessions.',
  'station' => 'ATS Station',
  'timeframe' => 'Zeitraum',
);
