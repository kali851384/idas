package com.idas.app.network

import android.os.Build
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import org.json.JSONObject
import java.io.OutputStreamWriter
import java.net.HttpURLConnection
import java.net.URL
import java.net.URLEncoder

object ApiService {

    private val BASE = if (Build.FINGERPRINT.contains("generic") ||
        Build.FINGERPRINT.contains("emulator") ||
        Build.MODEL.contains("Emulator") ||
        Build.MODEL.contains("Android SDK"))
        "http://10.0.2.2/IDAS/backend/api"
    else
        "http://192.168.174.1/IDAS/backend/api"

    fun get(endpoint: String, token: String, params: Map<String,String> = emptyMap()): JSONObject {
        val query = buildString {
            append("token=").append(URLEncoder.encode(token, "UTF-8"))
            params.forEach { (k, v) -> append("&$k=").append(URLEncoder.encode(v, "UTF-8")) }
        }
        val url  = URL("$BASE/$endpoint?$query")
        val conn = url.openConnection() as HttpURLConnection
        conn.requestMethod = "GET"; conn.connectTimeout = 8000; conn.readTimeout = 8000
        val response = conn.inputStream.bufferedReader().readText()
        conn.disconnect()
        return JSONObject(response)
    }

    fun post(endpoint: String, params: Map<String,String>): JSONObject {
        val body = params.entries.joinToString("&") { (k, v) ->
            "${URLEncoder.encode(k,"UTF-8")}=${URLEncoder.encode(v,"UTF-8")}"
        }
        val url  = URL("$BASE/$endpoint")
        val conn = url.openConnection() as HttpURLConnection
        conn.requestMethod = "POST"; conn.doOutput = true
        conn.connectTimeout = 8000; conn.readTimeout = 8000
        conn.setRequestProperty("Content-Type","application/x-www-form-urlencoded")
        OutputStreamWriter(conn.outputStream).use { it.write(body) }
        val response = conn.inputStream.bufferedReader().readText()
        conn.disconnect()
        return JSONObject(response)
    }

    // Auth
    suspend fun login(email: String, passwort: String): JSONObject =
        withContext(Dispatchers.IO) { post("login.php", mapOf("email" to email, "passwort" to passwort)) }

    suspend fun register(vorname: String, nachname: String, email: String,
        passwort: String, geburtsdatum: String, geschlecht: String): JSONObject =
        withContext(Dispatchers.IO) {
            post("register.php", mapOf("vorname" to vorname, "nachname" to nachname,
                "email" to email, "passwort" to passwort,
                "geburtsdatum" to geburtsdatum, "geschlecht" to geschlecht))
        }

    // Symptome
    suspend fun getSymptome(token: String): JSONObject =
        withContext(Dispatchers.IO) { get("symptome.php", token) }

    // Matching
    suspend fun getMatching(token: String, symptomIds: String): JSONObject =
        withContext(Dispatchers.IO) { get("matching.php", token, mapOf("symptome" to symptomIds)) }

    // Termine
    suspend fun getTermine(token: String): JSONObject =
        withContext(Dispatchers.IO) { get("termine.php", token) }

    suspend fun buchTermin(token: String, arztId: Int, datum: String, beschreibung: String, symptomIds: String = ""): JSONObject =
        withContext(Dispatchers.IO) {
            post("termin_buchen.php", mapOf("token" to token, "arzt_id" to arztId.toString(),
                "datum" to datum, "beschreibung" to beschreibung, "symptom_ids" to symptomIds))
        }

    suspend fun cancelTermin(token: String, terminId: Int): JSONObject =
        withContext(Dispatchers.IO) {
            post("termine.php", mapOf("token" to token, "termin_id" to terminId.toString()))
        }

    // Profil
    suspend fun getProfil(token: String): JSONObject =
        withContext(Dispatchers.IO) { get("profil.php", token) }

    suspend fun updateProfil(token: String, fields: Map<String,String>): JSONObject =
        withContext(Dispatchers.IO) { post("profil.php", fields + mapOf("token" to token)) }

    // Vorerkrankungen
    suspend fun getVorerkrankungen(token: String): JSONObject =
        withContext(Dispatchers.IO) { get("vorerkrankungen.php", token) }

    suspend fun addVorerkrankung(token: String, name: String, seit: String): JSONObject =
        withContext(Dispatchers.IO) {
            post("vorerkrankungen.php", mapOf("token" to token, "name" to name, "seit" to seit))
        }

    suspend fun editVorerkrankung(token: String, id: Int, seit: String): JSONObject =
        withContext(Dispatchers.IO) {
            post("vorerkrankungen.php", mapOf(
                "token" to token, "_method" to "PUT",
                "id" to id.toString(), "seit" to seit
            ))
        }

    // Support
    suspend fun getSupportTickets(token: String): JSONObject =
        withContext(Dispatchers.IO) { get("support.php", token) }

    suspend fun createSupportTicket(token: String, betreff: String, problem: String): JSONObject =
        withContext(Dispatchers.IO) {
            post("support.php", mapOf("token" to token,
                "betreff" to betreff, "problembeschreibung" to problem))
        }
}
