package com.idas.app.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.models.Profil
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.launch

data class Vorerkrankung(val id: Int, val name: String, val seit: String)

val PREDEFINED_DE = listOf(
    "Diabetes Typ 1", "Diabetes Typ 2", "Bluthochdruck", "Herzerkrankung",
    "Herzrhythmusstörung", "Asthma", "COPD", "Schilddrüsenerkrankung",
    "Nierenerkrankung", "Lebererkrankung", "Rheuma", "Arthrose",
    "Osteoporose", "Depression", "Angststörung", "Epilepsie",
    "Multiple Sklerose", "Parkinson", "Krebs (in Remission)",
    "Allergien", "Laktoseintoleranz", "Glutenunverträglichkeit",
    "Migräne", "Rückenschmerzen (chronisch)"
)
val PREDEFINED_EN = listOf(
    "Type 1 Diabetes", "Type 2 Diabetes", "High Blood Pressure", "Heart Disease",
    "Heart Arrhythmia", "Asthma", "COPD", "Thyroid Disease",
    "Kidney Disease", "Liver Disease", "Rheumatism", "Osteoarthritis",
    "Osteoporosis", "Depression", "Anxiety Disorder", "Epilepsy",
    "Multiple Sclerosis", "Parkinson's Disease", "Cancer (in Remission)",
    "Allergies", "Lactose Intolerance", "Gluten Intolerance",
    "Migraine", "Chronic Back Pain"
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProfilScreen(
    token: String,
    darkMode: Boolean = false,
    onDarkModeToggle: (Boolean) -> Unit = {},
    onBack: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val scope           = rememberCoroutineScope()
    var profil          by remember { mutableStateOf<Profil?>(null) }
    var loading         by remember { mutableStateOf(true) }
    var editing         by remember { mutableStateOf(false) }
    var error           by remember { mutableStateOf("") }
    var saved           by remember { mutableStateOf("") }
    var vorerkrankungen by remember { mutableStateOf<List<Vorerkrankung>>(emptyList()) }
    var showAddVE       by remember { mutableStateOf(false) }
    var editingVEId     by remember { mutableStateOf<Int?>(null) }
    var veLoading       by remember { mutableStateOf(false) }
    var veError         by remember { mutableStateOf("") }
    var selectedConditions by remember { mutableStateOf<Map<String, String>>(emptyMap()) }
    var customName      by remember { mutableStateOf("") }
    var customSeit      by remember { mutableStateOf("") }

    var vorname      by remember { mutableStateOf("") }
    var nachname     by remember { mutableStateOf("") }
    var email        by remember { mutableStateOf("") }
    var telefon      by remember { mutableStateOf("") }
    var wohnort      by remember { mutableStateOf("") }
    var plz          by remember { mutableStateOf("") }
    var adresse      by remember { mutableStateOf("") }
    var geburtsdatum by remember { mutableStateOf("") }

    fun loadProfil() {
        scope.launch {
            loading = true
            try {
                val res = ApiService.getProfil(token)
                if (res.getBoolean("success")) {
                    val d = res.getJSONObject("data")
                    val p = Profil(d.getInt("patient_id"),
                        d.optString("vorname",""), d.optString("nachname",""),
                        d.optString("email",""), d.optString("telefon",""),
                        d.optString("wohnort",""), d.optString("plz",""),
                        d.optString("adresse",""), d.optString("geburtsdatum",""),
                        d.optString("geschlecht",""))
                    profil = p
                    vorname = p.vorname; nachname = p.nachname; email = p.email
                    telefon = p.telefon; wohnort = p.wohnort; plz = p.plz
                    adresse = p.adresse; geburtsdatum = p.geburtsdatum
                }
            } catch (e: Exception) { error = Strings.get("error_server") }
            finally { loading = false }
        }
    }

    fun loadVE() {
        scope.launch {
            try {
                val res = ApiService.getVorerkrankungen(token)
                if (res.getBoolean("success")) {
                    val arr = res.getJSONArray("data")
                    vorerkrankungen = (0 until arr.length()).map {
                        val o = arr.getJSONObject(it)
                        Vorerkrankung(o.getInt("id"), o.optString("name",""), o.optString("seit",""))
                    }
                }
            } catch (e: Exception) { }
        }
    }

    LaunchedEffect(Unit) { loadProfil(); loadVE() }

    val predefined = if (Strings.language == "de") PREDEFINED_DE else PREDEFINED_EN

    // Add dialog
    if (showAddVE) {
        AlertDialog(
            onDismissRequest = { showAddVE = false; selectedConditions = emptyMap(); customName = ""; customSeit = "" },
            title = { Text(if (Strings.language == "de") "Vorerkrankungen auswählen" else "Select conditions", fontWeight = FontWeight.Bold) },
            text = {
                Column(modifier = Modifier.heightIn(max = 460.dp).verticalScroll(rememberScrollState())) {
                    if (veError.isNotEmpty()) {
                        Text(veError, color = IDASRed, fontSize = 12.sp, modifier = Modifier.padding(bottom = 8.dp))
                    }
                    Text(
                        if (Strings.language == "de") "Häufige Erkrankungen:" else "Common conditions:",
                        fontSize = 12.sp, color = textSec, fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(bottom = 8.dp)
                    )
                    predefined.forEach { condition ->
                        val alreadyAdded = vorerkrankungen.any { it.name.equals(condition, ignoreCase = true) }
                        val isSelected   = condition in selectedConditions
                        val seitValue    = selectedConditions[condition] ?: ""

                        Column(
                            modifier = Modifier.fillMaxWidth()
                                .background(when { alreadyAdded -> Color(0xFFF5F5F5); isSelected -> IDASBlueLight; else -> Color.Transparent })
                                .clickable(enabled = !alreadyAdded) {
                                    selectedConditions = if (isSelected) selectedConditions - condition
                                    else selectedConditions + (condition to "")
                                }
                                .padding(vertical = 6.dp, horizontal = 4.dp)
                        ) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Checkbox(
                                    checked = isSelected || alreadyAdded,
                                    onCheckedChange = null,
                                    enabled = !alreadyAdded,
                                    colors = CheckboxDefaults.colors(
                                        checkedColor = if (alreadyAdded) IDASTextSecondary else IDASBlue)
                                )
                                Spacer(Modifier.width(6.dp))
                                Column(modifier = Modifier.weight(1f)) {
                                    Text(condition, fontSize = 13.sp,
                                        color = if (alreadyAdded) IDASTextSecondary else IDASTextPrimary,
                                        fontWeight = if (isSelected) FontWeight.SemiBold else FontWeight.Normal)
                                    if (alreadyAdded) Text(
                                        if (Strings.language == "de") "Bereits eingetragen" else "Already added",
                                        fontSize = 10.sp, color = textSec)
                                }
                            }
                            if (isSelected) {
                                OutlinedTextField(
                                    value = seitValue,
                                    onValueChange = { selectedConditions = selectedConditions + (condition to it) },
                                    placeholder = { Text(if (Strings.language == "de") "Seit (z.B. 2019)" else "Since (e.g. 2019)", fontSize = 12.sp) },
                                    singleLine = true,
                                    modifier = Modifier.fillMaxWidth().padding(start = 36.dp, top = 4.dp, bottom = 4.dp),
                                    shape = RoundedCornerShape(8.dp),
                                    colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = IDASBlue)
                                )
                            }
                        }
                        Divider(color = border, thickness = 0.5.dp)
                    }
                    Spacer(Modifier.height(16.dp))
                    Text("✏️  ${if (Strings.language == "de") "Sonstige:" else "Other:"}",
                        fontSize = 12.sp, color = textSec, fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(bottom = 6.dp))
                    OutlinedTextField(value = customName, onValueChange = { customName = it },
                        placeholder = { Text(if (Strings.language == "de") "Eigene Erkrankung…" else "Custom condition…", fontSize = 13.sp) },
                        singleLine = true, modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = IDASBlue))
                    Spacer(Modifier.height(8.dp))
                    OutlinedTextField(value = customSeit, onValueChange = { customSeit = it },
                        placeholder = { Text(if (Strings.language == "de") "Seit (z.B. 2019)" else "Since (e.g. 2019)", fontSize = 13.sp) },
                        singleLine = true, modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(12.dp),
                        colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = IDASBlue))
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        if (selectedConditions.isEmpty() && customName.isBlank()) {
                            veError = if (Strings.language == "de") "Bitte mindestens eine Erkrankung auswählen." else "Please select at least one."; return@Button
                        }
                        veLoading = true; veError = ""
                        scope.launch {
                            try {
                                selectedConditions.forEach { (name, seit) -> ApiService.addVorerkrankung(token, name, seit) }
                                if (customName.isNotBlank()) ApiService.addVorerkrankung(token, customName, customSeit)
                                showAddVE = false; selectedConditions = emptyMap(); customName = ""; customSeit = ""
                                loadVE()
                            } catch (e: Exception) { veError = Strings.get("error_server") }
                            finally { veLoading = false }
                        }
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                    enabled = !veLoading
                ) {
                    if (veLoading) CircularProgressIndicator(color = Color.White, strokeWidth = 2.dp, modifier = Modifier.size(18.dp))
                    else Text(Strings.get("save"))
                }
            },
            dismissButton = {
                TextButton(onClick = { showAddVE = false; selectedConditions = emptyMap(); customName = ""; customSeit = ""; veError = "" }) {
                    Text(Strings.get("cancel"))
                }
            }
        )
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(Strings.get("profil_title")) },
                navigationIcon = { IconButton(onClick = onBack) { Icon(Icons.Default.ArrowBack, Strings.get("back")) } },
                actions = {
                    if (!editing && profil != null) {
                        IconButton(onClick = { editing = true; saved = "" }) {
                            Icon(Icons.Default.Edit, "Bearbeiten", tint = Color.White)
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = IDASBlue, titleContentColor = Color.White, navigationIconContentColor = Color.White)
            )
        }
    ) { padding ->
        when {
            loading -> Box(Modifier.fillMaxSize().padding(padding), Alignment.Center) { CircularProgressIndicator(color = IDASBlue) }
            profil == null -> Box(Modifier.fillMaxSize().padding(padding), Alignment.Center) { Text(error.ifBlank { "Profil nicht gefunden." }, color = IDASRed) }
            else -> {
                val p = profil!!
                Column(modifier = Modifier.fillMaxSize().background(bg).padding(padding).verticalScroll(rememberScrollState())) {
                    // Hero
                    Box(modifier = Modifier.fillMaxWidth()
                        .background(Brush.horizontalGradient(listOf(IDASBlueDark, IDASBlue))).padding(24.dp)) {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Box(modifier = Modifier.size(64.dp).clip(CircleShape).background(Color.White.copy(0.2f)),
                                contentAlignment = Alignment.Center) {
                                Text("${p.vorname.firstOrNull()?.uppercaseChar() ?: ""}${p.nachname.firstOrNull()?.uppercaseChar() ?: ""}",
                                    fontWeight = FontWeight.Bold, color = Color.White, fontSize = 22.sp)
                            }
                            Spacer(Modifier.width(16.dp))
                            Column {
                                Text("${p.vorname} ${p.nachname}", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 18.sp)
                                Text(p.email, color = Color.White.copy(0.8f), fontSize = 13.sp)
                                Surface(color = Color.White.copy(0.2f), shape = RoundedCornerShape(8.dp), modifier = Modifier.padding(top = 6.dp)) {
                                    Text("Patient #${p.patientId}", modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp), color = Color.White, fontSize = 11.sp)
                                }
                            }
                        }
                    }

                    Column(modifier = Modifier.padding(16.dp)) {
                        if (saved.isNotEmpty()) {
                            Surface(color = Color(0xFFE8F5E9), shape = RoundedCornerShape(12.dp), modifier = Modifier.fillMaxWidth()) {
                                Row(modifier = Modifier.padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                                    Text("✅", fontSize = 16.sp); Spacer(Modifier.width(8.dp))
                                    Text(saved, color = IDASGreenDark, fontSize = 13.sp)
                                }
                            }
                            Spacer(Modifier.height(12.dp))
                        }
                        if (error.isNotEmpty()) {
                            Surface(color = Color(0xFFFFEBEE), shape = RoundedCornerShape(12.dp), modifier = Modifier.fillMaxWidth()) {
                                Row(modifier = Modifier.padding(12.dp)) {
                                    Text("⚠️", fontSize = 16.sp); Spacer(Modifier.width(8.dp))
                                    Text(error, color = IDASRed, fontSize = 13.sp)
                                }
                            }
                            Spacer(Modifier.height(12.dp))
                        }

                        // Personal data card
                        Card(shape = RoundedCornerShape(20.dp), elevation = CardDefaults.cardElevation(2.dp),
                            colors = CardDefaults.cardColors(containerColor = cardColor), modifier = Modifier.fillMaxWidth()) {
                            Column(modifier = Modifier.padding(20.dp)) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Text(if (editing) "✏️" else "👤", fontSize = 18.sp); Spacer(Modifier.width(8.dp))
                                    Text(if (editing) Strings.get("profil_edit") else Strings.get("profil_data"),
                                        fontWeight = FontWeight.Bold, fontSize = 16.sp, color = textPri)
                                }
                                Divider(modifier = Modifier.padding(vertical = 14.dp), color = border)
                                if (!editing) {
                                    listOf(
                                        "👤 ${Strings.get("profil_vorname")}"    to p.vorname,
                                        "👤 ${Strings.get("profil_nachname")}"   to p.nachname,
                                        "📧 ${Strings.get("profil_email")}"      to p.email,
                                        "📞 ${Strings.get("profil_telefon")}"    to p.telefon,
                                        "🎂 ${Strings.get("profil_geb")}"        to p.geburtsdatum,
                                        "🏠 ${Strings.get("profil_adresse")}"    to p.adresse,
                                        "📮 ${Strings.get("profil_plz")}"        to p.plz,
                                        "📍 ${Strings.get("profil_wohnort")}"    to p.wohnort,
                                        "⚥ ${Strings.get("profil_geschlecht")}" to when(p.geschlecht) {
                                            "m" -> Strings.get("profil_male"); "w" -> Strings.get("profil_female"); else -> p.geschlecht }
                                    ).forEach { (label, value) ->
                                        Row(modifier = Modifier.fillMaxWidth().padding(vertical = 7.dp), verticalAlignment = Alignment.CenterVertically) {
                                            Text(label, fontSize = 13.sp, color = textSec, modifier = Modifier.width(140.dp))
                                            Text(value.ifBlank { "—" }, fontSize = 13.sp, fontWeight = FontWeight.Medium, color = textPri)
                                        }
                                        Divider(color = border, thickness = 0.5.dp)
                                    }
                                } else {
                                    @Composable fun EditField(label: String, value: String, onChange: (String) -> Unit) {
                                        OutlinedTextField(value = value, onValueChange = onChange, label = { Text(label) }, singleLine = true,
                                            modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp),
                                            colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue))
                                        Spacer(Modifier.height(10.dp))
                                    }
                                    EditField(Strings.get("profil_vorname"), vorname) { vorname = it }
                                    EditField(Strings.get("profil_nachname"), nachname) { nachname = it }
                                    EditField(Strings.get("profil_email"), email) { email = it }
                                    EditField(Strings.get("profil_telefon"), telefon) { telefon = it }
                                    EditField("${Strings.get("profil_geb")} (YYYY-MM-DD)", geburtsdatum) { geburtsdatum = it }
                                    EditField(Strings.get("profil_adresse"), adresse) { adresse = it }
                                    EditField(Strings.get("profil_plz"), plz) { plz = it }
                                    EditField(Strings.get("profil_wohnort"), wohnort) { wohnort = it }
                                    Row(horizontalArrangement = Arrangement.spacedBy(10.dp)) {
                                        OutlinedButton(onClick = { editing = false; error = "" },
                                            modifier = Modifier.weight(1f).height(46.dp), shape = RoundedCornerShape(12.dp)) { Text(Strings.get("cancel")) }
                                        Button(onClick = {
                                            scope.launch {
                                                try {
                                                    val res = ApiService.updateProfil(token, mapOf(
                                                        "vorname" to vorname, "nachname" to nachname, "email" to email,
                                                        "telefon" to telefon, "wohnort" to wohnort, "plz" to plz,
                                                        "adresse" to adresse, "geburtsdatum" to geburtsdatum, "geschlecht" to p.geschlecht))
                                                    if (res.getBoolean("success")) { saved = Strings.get("profil_saved"); editing = false; loadProfil() }
                                                    else error = res.optString("message","Fehler.")
                                                } catch (e: Exception) { error = Strings.get("error_server") }
                                            }
                                        }, modifier = Modifier.weight(1f).height(46.dp),
                                            colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                                            shape = RoundedCornerShape(12.dp)) {
                                            Icon(Icons.Default.Save, null, modifier = Modifier.size(16.dp))
                                            Spacer(Modifier.width(4.dp))
                                            Text(Strings.get("save"), fontWeight = FontWeight.Bold)
                                        }
                                    }
                                }
                            }
                        }

                        Spacer(Modifier.height(16.dp))

                        // Vorerkrankungen Card
                        Card(shape = RoundedCornerShape(20.dp), elevation = CardDefaults.cardElevation(2.dp),
                            colors = CardDefaults.cardColors(containerColor = cardColor), modifier = Modifier.fillMaxWidth()) {
                            Column(modifier = Modifier.padding(20.dp)) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Text("🏥", fontSize = 18.sp); Spacer(Modifier.width(8.dp))
                                    Text(if (Strings.language == "de") "Vorerkrankungen" else "Pre-existing Conditions",
                                        fontWeight = FontWeight.Bold, fontSize = 16.sp, color = textPri, modifier = Modifier.weight(1f))
                                    IconButton(onClick = { showAddVE = true }, modifier = Modifier.size(32.dp)) {
                                        Icon(Icons.Default.Add, null, tint = IDASBlue, modifier = Modifier.size(22.dp))
                                    }
                                }
                                Divider(modifier = Modifier.padding(vertical = 12.dp), color = border)

                                if (vorerkrankungen.isEmpty()) {
                                    Box(modifier = Modifier.fillMaxWidth().padding(vertical = 16.dp), contentAlignment = Alignment.Center) {
                                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                            Text("💊", fontSize = 32.sp); Spacer(Modifier.height(8.dp))
                                            Text(if (Strings.language == "de") "Keine Vorerkrankungen eingetragen" else "No conditions added yet",
                                                color = textSec, fontSize = 13.sp)
                                            TextButton(onClick = { showAddVE = true }) {
                                                Text(if (Strings.language == "de") "+ Hinzufügen" else "+ Add", color = IDASBlue, fontWeight = FontWeight.Bold)
                                            }
                                        }
                                    }
                                } else {
                                    vorerkrankungen.forEach { ve ->
                                        val isThisEditing = editingVEId == ve.id
                                        VERow(
                                            ve          = ve,
                                            token       = token,
                                            scope       = scope,
                                            isEditing   = isThisEditing,
                                            onEdit      = { editingVEId = ve.id },
                                            onCancelEdit= { editingVEId = null },
                                            onSaved     = { editingVEId = null; loadVE() },
                                            onDeleted   = { loadVE() }
                                        )
                                        Divider(color = border, thickness = 0.5.dp)
                                    }
                                    Spacer(Modifier.height(8.dp))
                                    TextButton(onClick = { showAddVE = true }, modifier = Modifier.align(Alignment.End)) {
                                        Icon(Icons.Default.Add, null, modifier = Modifier.size(16.dp)); Spacer(Modifier.width(4.dp))
                                        Text(if (Strings.language == "de") "Weitere hinzufügen" else "Add more", color = IDASBlue, fontSize = 13.sp)
                                    }
                                }
                            }
                        }
                    }
                    Spacer(Modifier.height(20.dp))
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun VERow(
    ve: Vorerkrankung,
    token: String,
    scope: CoroutineScope,
    isEditing: Boolean,
    onEdit: () -> Unit,
    onCancelEdit: () -> Unit,
    onSaved: () -> Unit,
    onDeleted: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    var seitEdit   by remember(ve.id) { mutableStateOf(ve.seit) }
    var saving     by remember { mutableStateOf(false) }
    var showDelete by remember { mutableStateOf(false) }

    if (showDelete) {
        AlertDialog(
            onDismissRequest = { showDelete = false },
            title = { Text(if (Strings.language == "de") "Löschen?" else "Delete?") },
            text  = { Text(if (Strings.language == "de") "\"${ve.name}\" wirklich löschen?" else "Really delete \"${ve.name}\"?") },
            confirmButton = {
                TextButton(onClick = {
                    showDelete = false
                    scope.launch {
                        try {
                            ApiService.post("vorerkrankungen.php", mapOf("token" to token, "_method" to "DELETE", "id" to ve.id.toString()))
                            onDeleted()
                        } catch (e: Exception) { }
                    }
                }) { Text(if (Strings.language == "de") "Löschen" else "Delete", color = IDASRed) }
            },
            dismissButton = { TextButton(onClick = { showDelete = false }) { Text(Strings.get("cancel")) } }
        )
    }

    // Name row
    Row(modifier = Modifier.fillMaxWidth().padding(vertical = 8.dp), verticalAlignment = Alignment.CenterVertically) {
        Box(modifier = Modifier.size(36.dp).clip(RoundedCornerShape(10.dp)).background(Color(0xFFFFEBEE)),
            contentAlignment = Alignment.Center) { Text("🩺", fontSize = 16.sp) }
        Spacer(Modifier.width(12.dp))
        Column(modifier = Modifier.weight(1f)) {
            Text(ve.name, fontSize = 14.sp, fontWeight = FontWeight.Medium, color = textPri)
            if (!isEditing) {
                Text(
                    if (ve.seit.isNotBlank()) "${if (Strings.language == "de") "Seit" else "Since"} ${ve.seit}"
                    else if (Strings.language == "de") "Kein Jahr — ✏️ tippen" else "No year — ✏️ tap",
                    fontSize = 12.sp,
                    color = if (ve.seit.isBlank()) IDASBlue.copy(0.7f) else IDASTextSecondary
                )
            }
        }
        IconButton(onClick = { if (isEditing) onCancelEdit() else { seitEdit = ve.seit; onEdit() } }, modifier = Modifier.size(34.dp)) {
            Icon(if (isEditing) Icons.Default.Close else Icons.Default.Edit, null, tint = textSec, modifier = Modifier.size(16.dp))
        }
        IconButton(onClick = { showDelete = true }, modifier = Modifier.size(34.dp)) {
            Icon(Icons.Default.Delete, null, tint = IDASRed, modifier = Modifier.size(16.dp))
        }
    }

    // Edit section — full width, below the name row
    if (isEditing) {
        Column(modifier = Modifier.fillMaxWidth().padding(bottom = 8.dp)) {
            OutlinedTextField(
                value = seitEdit,
                onValueChange = { seitEdit = it },
                label = { Text(if (Strings.language == "de") "Seit (Jahr)" else "Since (Year)") },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(10.dp),
                colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = IDASBlue, focusedLabelColor = IDASBlue)
            )
            Spacer(Modifier.height(8.dp))
            Button(
                onClick = {
                    saving = true
                    scope.launch {
                        try {
                            ApiService.editVorerkrankung(token, ve.id, seitEdit)
                            onSaved()
                        } catch (e: Exception) { }
                        finally { saving = false }
                    }
                },
                modifier = Modifier.fillMaxWidth().height(44.dp),
                colors = ButtonDefaults.buttonColors(containerColor = IDASGreen),
                shape = RoundedCornerShape(10.dp)
            ) {
                if (saving) CircularProgressIndicator(color = Color.White, strokeWidth = 2.dp, modifier = Modifier.size(16.dp))
                else Text(Strings.get("save"), fontWeight = FontWeight.Bold)
            }
        }
    }
}
