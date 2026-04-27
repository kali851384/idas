package com.idas.app.utils

object Strings {
    var language = "de" // default German

    val data = mapOf(
        // General
        "app_name"          to mapOf("de" to "IDAS", "en" to "IDAS"),
        "loading"           to mapOf("de" to "Laden…", "en" to "Loading…"),
        "error_server"      to mapOf("de" to "Server nicht erreichbar.", "en" to "Server not reachable."),
        "save"              to mapOf("de" to "Speichern", "en" to "Save"),
        "cancel"            to mapOf("de" to "Abbrechen", "en" to "Cancel"),
        "back"              to mapOf("de" to "Zurück", "en" to "Back"),
        "logout"            to mapOf("de" to "Logout", "en" to "Logout"),
        "error"             to mapOf("de" to "Fehler", "en" to "Error"),

        // Login
        "login_title"       to mapOf("de" to "Willkommen zurück", "en" to "Welcome back"),
        "login_subtitle"    to mapOf("de" to "Bitte melden Sie sich an", "en" to "Please sign in"),
        "login_email"       to mapOf("de" to "E-Mail", "en" to "Email"),
        "login_password"    to mapOf("de" to "Passwort", "en" to "Password"),
        "login_button"      to mapOf("de" to "Anmelden", "en" to "Sign in"),
        "login_no_account"  to mapOf("de" to "Noch kein Konto? ", "en" to "No account yet? "),
        "login_register"    to mapOf("de" to "Registrieren", "en" to "Register"),
        "login_error_empty" to mapOf("de" to "Bitte alle Felder ausfüllen.", "en" to "Please fill in all fields."),
        "login_error_fail"  to mapOf("de" to "Login fehlgeschlagen.", "en" to "Login failed."),

        // Register
        "register_title"    to mapOf("de" to "Konto erstellen", "en" to "Create account"),
        "register_vorname"  to mapOf("de" to "Vorname *", "en" to "First name *"),
        "register_nachname" to mapOf("de" to "Nachname *", "en" to "Last name *"),
        "register_email"    to mapOf("de" to "E-Mail *", "en" to "Email *"),
        "register_password" to mapOf("de" to "Passwort *", "en" to "Password *"),
        "register_geb"      to mapOf("de" to "Geburtsdatum * (YYYY-MM-DD)", "en" to "Date of birth * (YYYY-MM-DD)"),
        "register_gender"   to mapOf("de" to "Geschlecht", "en" to "Gender"),
        "register_male"     to mapOf("de" to "Männlich", "en" to "Male"),
        "register_female"   to mapOf("de" to "Weiblich", "en" to "Female"),
        "register_diverse"  to mapOf("de" to "Divers", "en" to "Other"),
        "register_button"   to mapOf("de" to "Konto erstellen", "en" to "Create account"),
        "register_error_empty" to mapOf("de" to "Bitte alle Pflichtfelder ausfüllen.", "en" to "Please fill in all required fields."),
        "register_error_pw" to mapOf("de" to "Passwort min. 6 Zeichen.", "en" to "Password min. 6 characters."),
        "register_success"  to mapOf("de" to "Registrierung erfolgreich.", "en" to "Registration successful."),

        // Dashboard
        "dashboard_hello"   to mapOf("de" to "Hallo", "en" to "Hello"),
        "dashboard_help"    to mapOf("de" to "Wie können wir Ihnen heute helfen?", "en" to "How can we help you today?"),
        "dashboard_find"    to mapOf("de" to "Arzt finden →", "en" to "Find doctor →"),
        "dashboard_symptom" to mapOf("de" to "Symptom-Check", "en" to "Symptom Check"),
        "dashboard_symptom_sub" to mapOf("de" to "Symptome eingeben & passenden Arzt finden", "en" to "Enter symptoms & find the right doctor"),
        "dashboard_termine" to mapOf("de" to "Meine Termine", "en" to "My Appointments"),
        "dashboard_termine_sub" to mapOf("de" to "Bevorstehende und vergangene Termine", "en" to "Upcoming and past appointments"),
        "dashboard_profil"  to mapOf("de" to "Mein Profil", "en" to "My Profile"),
        "dashboard_profil_sub" to mapOf("de" to "Persönliche Daten verwalten", "en" to "Manage personal data"),
        "dashboard_tip"     to mapOf("de" to "IDAS Tipp", "en" to "IDAS Tip"),
        "dashboard_tip_text" to mapOf("de" to "Wählen Sie Ihre Symptome aus — wir empfehlen den passenden Facharzt.", "en" to "Select your symptoms — we recommend the right specialist."),

        // Symptoms
        "symptom_title"     to mapOf("de" to "Symptome auswählen", "en" to "Select symptoms"),
        "symptom_hint"      to mapOf("de" to "Tippen Sie auf einen Körperbereich", "en" to "Tap a body area"),
        "symptom_all"       to mapOf("de" to "Alle", "en" to "All"),
        "symptom_body"      to mapOf("de" to "Körper", "en" to "Body"),
        "symptom_search"    to mapOf("de" to "Symptom suchen…", "en" to "Search symptom…"),
        "symptom_button"    to mapOf("de" to "Arzt finden", "en" to "Find doctor"),
        "symptom_select"    to mapOf("de" to "Symptome auswählen", "en" to "Select symptoms"),
        "symptom_count"     to mapOf("de" to "Symptome", "en" to "symptoms"),

        // Matching
        "matching_title"    to mapOf("de" to "Empfohlene Ärzte", "en" to "Recommended Doctors"),
        "matching_loading"  to mapOf("de" to "Analysiere Symptome…", "en" to "Analysing symptoms…"),
        "matching_based"    to mapOf("de" to "Basierend auf Ihren Symptomen empfehlen wir:", "en" to "Based on your symptoms we recommend:"),
        "matching_book"     to mapOf("de" to "Termin buchen", "en" to "Book appointment"),
        "matching_rec"      to mapOf("de" to "Empfehlung", "en" to "Recommendation"),
        "matching_pts"      to mapOf("de" to "Pkt", "en" to "pts"),

        // Booking
        "booking_title"     to mapOf("de" to "Termin buchen", "en" to "Book appointment"),
        "booking_date"      to mapOf("de" to "DATUM *", "en" to "DATE *"),
        "booking_date_hint" to mapOf("de" to "YYYY-MM-DD", "en" to "YYYY-MM-DD"),
        "booking_time"      to mapOf("de" to "UHRZEIT *", "en" to "TIME *"),
        "booking_reason"    to mapOf("de" to "GRUND (optional)", "en" to "REASON (optional)"),
        "booking_reason_hint" to mapOf("de" to "Beschreibung Ihres Anliegens…", "en" to "Description of your concern…"),
        "booking_confirm"   to mapOf("de" to "✓ Termin bestätigen", "en" to "✓ Confirm appointment"),
        "booking_error_date" to mapOf("de" to "Bitte ein Datum eingeben.", "en" to "Please enter a date."),
        "booking_error_format" to mapOf("de" to "Format: YYYY-MM-DD", "en" to "Format: YYYY-MM-DD"),
        "booking_error_fail" to mapOf("de" to "Buchung fehlgeschlagen.", "en" to "Booking failed."),

        // Confirmation
        "confirm_title"     to mapOf("de" to "Buchungsbestätigung", "en" to "Booking Confirmation"),
        "confirm_booked"    to mapOf("de" to "Termin gebucht!", "en" to "Appointment booked!"),
        "confirm_success"   to mapOf("de" to "Ihr Termin wurde erfolgreich reserviert.", "en" to "Your appointment has been successfully reserved."),
        "confirm_details"   to mapOf("de" to "Termindetails", "en" to "Appointment details"),
        "confirm_doctor"    to mapOf("de" to "🏥 Arzt", "en" to "🏥 Doctor"),
        "confirm_fach"      to mapOf("de" to "🔬 Fachbereich", "en" to "🔬 Specialty"),
        "confirm_date"      to mapOf("de" to "📅 Datum", "en" to "📅 Date"),
        "confirm_patient"   to mapOf("de" to "👤 Patient", "en" to "👤 Patient"),
        "confirm_reason"    to mapOf("de" to "📝 Grund", "en" to "📝 Reason"),
        "confirm_ticket"    to mapOf("de" to "Ticket", "en" to "Ticket"),
        "confirm_pdf"       to mapOf("de" to "Als PDF speichern / teilen", "en" to "Save / share as PDF"),
        "confirm_termine"   to mapOf("de" to "Meine Termine ansehen", "en" to "View my appointments"),
        "confirm_home"      to mapOf("de" to "Zurück zum Dashboard", "en" to "Back to dashboard"),

        // Termine
        "termine_title"     to mapOf("de" to "Meine Termine", "en" to "My Appointments"),
        "termine_empty"     to mapOf("de" to "Keine Termine vorhanden", "en" to "No appointments yet"),
        "termine_empty_sub" to mapOf("de" to "Buchen Sie Ihren ersten Termin", "en" to "Book your first appointment"),
        "termine_upcoming"  to mapOf("de" to "Bevorstehend", "en" to "Upcoming"),
        "termine_past"      to mapOf("de" to "Abgeschlossen", "en" to "Completed"),
        "termine_cancel"    to mapOf("de" to "Termin absagen", "en" to "Cancel appointment"),
        "termine_cancel_q"  to mapOf("de" to "Möchten Sie diesen Termin wirklich absagen?", "en" to "Do you really want to cancel this appointment?"),
        "termine_cancel_yes" to mapOf("de" to "Absagen", "en" to "Cancel"),
        "termine_status_up" to mapOf("de" to "Bevorstehend", "en" to "Upcoming"),
        "termine_status_done" to mapOf("de" to "Erledigt", "en" to "Done"),

        // Profil
        "profil_title"      to mapOf("de" to "Mein Profil", "en" to "My Profile"),
        "profil_data"       to mapOf("de" to "Persönliche Daten", "en" to "Personal data"),
        "profil_edit"       to mapOf("de" to "Daten bearbeiten", "en" to "Edit data"),
        "profil_saved"      to mapOf("de" to "Profil erfolgreich aktualisiert.", "en" to "Profile updated successfully."),
        "profil_vorname"    to mapOf("de" to "Vorname", "en" to "First name"),
        "profil_nachname"   to mapOf("de" to "Nachname", "en" to "Last name"),
        "profil_email"      to mapOf("de" to "E-Mail", "en" to "Email"),
        "profil_telefon"    to mapOf("de" to "Telefon", "en" to "Phone"),
        "profil_geb"        to mapOf("de" to "Geburtsdatum", "en" to "Date of birth"),
        "profil_adresse"    to mapOf("de" to "Adresse", "en" to "Address"),
        "profil_plz"        to mapOf("de" to "PLZ", "en" to "Postal code"),
        "profil_wohnort"    to mapOf("de" to "Wohnort", "en" to "City"),
        "profil_geschlecht" to mapOf("de" to "Geschlecht", "en" to "Gender"),
        "profil_male"       to mapOf("de" to "Männlich", "en" to "Male"),
        "profil_female"     to mapOf("de" to "Weiblich", "en" to "Female"),

        // Onboarding
        "onboard_1_title"   to mapOf("de" to "Willkommen bei IDAS", "en" to "Welcome to IDAS"),
        "onboard_1_text"    to mapOf("de" to "Ihr persönliches Gesundheitsportal für Hannover.", "en" to "Your personal health portal for Hannover."),
        "onboard_2_title"   to mapOf("de" to "Symptome eingeben", "en" to "Enter symptoms"),
        "onboard_2_text"    to mapOf("de" to "Wählen Sie Ihre Symptome aus und wir finden den passenden Arzt.", "en" to "Select your symptoms and we find the right doctor for you."),
        "onboard_3_title"   to mapOf("de" to "Termin buchen", "en" to "Book appointment"),
        "onboard_3_text"    to mapOf("de" to "Buchen Sie direkt einen Termin und erhalten Sie eine Bestätigung als PDF.", "en" to "Book an appointment directly and receive a PDF confirmation."),
        "onboard_start"     to mapOf("de" to "Los geht's", "en" to "Get started"),
        "onboard_next"      to mapOf("de" to "Weiter", "en" to "Next"),
        "onboard_skip"      to mapOf("de" to "Überspringen", "en" to "Skip"),

        // Support
        "support_title"     to mapOf("de" to "Support", "en" to "Support"),
        "support_betreff"   to mapOf("de" to "Betreff *", "en" to "Subject *"),
        "support_problem"   to mapOf("de" to "Problem beschreiben *", "en" to "Describe problem *"),
        "support_send"      to mapOf("de" to "Ticket senden", "en" to "Send ticket"),
        "support_success"   to mapOf("de" to "Ticket erfolgreich gesendet.", "en" to "Ticket sent successfully."),
        "support_my"        to mapOf("de" to "Meine Tickets", "en" to "My tickets"),
        "support_empty"     to mapOf("de" to "Keine Tickets vorhanden.", "en" to "No tickets yet."),
        "support_open"      to mapOf("de" to "Offen", "en" to "Open"),
        "support_progress"  to mapOf("de" to "In Bearbeitung", "en" to "In progress"),
        "support_closed"    to mapOf("de" to "Geschlossen", "en" to "Closed"),

        // Notifications
        "notif_reminder"    to mapOf("de" to "Terminerinnerung", "en" to "Appointment reminder"),
        "notif_tomorrow"    to mapOf("de" to "Morgen", "en" to "Tomorrow"),
        "notif_today"       to mapOf("de" to "Heute", "en" to "Today"),
        "notif_at"          to mapOf("de" to "um", "en" to "at"),
    )

    fun get(key: String): String =
        data[key]?.get(language) ?: data[key]?.get("de") ?: key
}
