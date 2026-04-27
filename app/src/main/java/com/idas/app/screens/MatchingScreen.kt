package com.idas.app.screens

import android.Manifest
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.animation.*
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
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
import com.idas.app.models.Arzt
import com.idas.app.models.FachbereichResult
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings

// Hardcoded city coordinates for instant distance calculation
val CITY_COORDS = mapOf(
    "hannover"         to Pair(52.3759, 9.7320),
    "berlin"           to Pair(52.5200, 13.4050),
    "hamburg"          to Pair(53.5511, 9.9937),
    "münchen"          to Pair(48.1351, 11.5820),
    "munich"           to Pair(48.1351, 11.5820),
    "köln"             to Pair(50.9333, 6.9500),
    "koeln"            to Pair(50.9333, 6.9500),
    "frankfurt"        to Pair(50.1109, 8.6821),
    "stuttgart"        to Pair(48.7758, 9.1829),
    "düsseldorf"       to Pair(51.2217, 6.7762),
    "dortmund"         to Pair(51.5136, 7.4653),
    "essen"            to Pair(51.4508, 7.0131),
    "leipzig"          to Pair(51.3397, 12.3731),
    "dresden"          to Pair(51.0504, 13.7373),
    "nürnberg"         to Pair(49.4521, 11.0767),
    "nuernberg"        to Pair(49.4521, 11.0767),
    "bremen"           to Pair(53.0793, 8.8017),
    "bochum"           to Pair(51.4818, 7.2162),
    "wuppertal"        to Pair(51.2562, 7.1508),
    "bielefeld"        to Pair(52.0302, 8.5325),
    "mannheim"         to Pair(49.4875, 8.4660),
    "bonn"             to Pair(50.7374, 7.0982),
    "karlsruhe"        to Pair(49.0069, 8.4037),
    "münster"          to Pair(51.9607, 7.6261),
    "augsburg"         to Pair(48.3705, 10.8978),
    "wiesbaden"        to Pair(50.0782, 8.2398),
    "mönchengladbach"  to Pair(51.1805, 6.4428),
    "gelsenkirchen"    to Pair(51.5177, 7.0857),
    "aachen"           to Pair(50.7753, 6.0839),
    "braunschweig"     to Pair(52.2689, 10.5268),
    "kiel"             to Pair(54.3233, 10.1228),
    "chemnitz"         to Pair(50.8323, 12.9231),
    "halle"            to Pair(51.4826, 11.9696),
    "magdeburg"        to Pair(52.1205, 11.6276),
    "freiburg"         to Pair(47.9990, 7.8421),
    "erfurt"           to Pair(50.9787, 11.0328),
    "rostock"          to Pair(54.0924, 12.1407),
    "mainz"            to Pair(49.9929, 8.2473),
    "kassel"           to Pair(51.3167, 9.4833),
    "saarbrücken"      to Pair(49.2354, 6.9969),
    "heidelberg"       to Pair(49.3988, 8.6724),
    "darmstadt"        to Pair(49.8728, 8.6512),
    "würzburg"         to Pair(49.7913, 9.9534),
    "regensburg"       to Pair(49.0134, 12.1016),
    "ingolstadt"       to Pair(48.7665, 11.4258),
    "hildesheim"       to Pair(52.1521, 9.9512),
    "wolfsburg"        to Pair(52.4231, 10.7872),
    "lüneburg"         to Pair(53.2494, 10.4073),
    "göttingen"        to Pair(51.5413, 9.9158),
    "osnabrück"        to Pair(52.2799, 8.0472),
    "oldenburg"        to Pair(53.1435, 8.2146),
    "bremerhaven"      to Pair(53.5468, 8.5897),
    "schwerin"         to Pair(53.6355, 11.4012),
    "flensburg"        to Pair(54.7820, 9.4366),
    "lübeck"           to Pair(53.8655, 10.6866),
    "jena"             to Pair(50.9273, 11.5892),
    "weimar"           to Pair(50.9795, 11.3235),
    "passau"           to Pair(48.5748, 13.4648),
    "bayreuth"         to Pair(49.9456, 11.5713),
    "kaiserslautern"   to Pair(49.4439, 7.7688),
    "ludwigshafen"     to Pair(49.4774, 8.4369),
    "reutlingen"       to Pair(48.4911, 9.2041),
    "duisburg"         to Pair(51.4325, 6.7627),
    "leverkusen"       to Pair(51.0459, 6.9878),
    "görlitz"          to Pair(51.1539, 14.9897),
    "neubrandenburg"   to Pair(53.5560, 13.2634),
    "neumünster"       to Pair(54.0743, 9.9862),
    "stade"            to Pair(53.5993, 9.4749),
    "gotha"            to Pair(50.9481, 10.7014)
)

fun getCoordsFromAddress(address: String): Pair<Double, Double>? {
    val lower = address.lowercase()
    return CITY_COORDS.entries.firstOrNull { lower.contains(it.key) }?.value
}




@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MatchingScreen(
    token: String,
    symptomIds: String,
    onBack: () -> Unit,
    onBook: (arztId: Int, arztName: String, fachbereich: String) -> Unit,
    onDoctorDetail: (arzt: Arzt, fachbereich: String) -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    var results     by remember { mutableStateOf<List<FachbereichResult>>(emptyList()) }
    var loading     by remember { mutableStateOf(true) }
    var error       by remember { mutableStateOf("") }
    var searchQuery by remember { mutableStateOf("") }
    var showSearch  by remember { mutableStateOf(false) }
    var userLat     by remember { mutableStateOf<Double?>(null) }
    var userLon     by remember { mutableStateOf<Double?>(null) }
    val context     = androidx.compose.ui.platform.LocalContext.current

    // Get user location
    val permLauncher = rememberLauncherForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { perms ->
        if (perms[Manifest.permission.ACCESS_FINE_LOCATION] == true ||
            perms[Manifest.permission.ACCESS_COARSE_LOCATION] == true) {
            val loc = com.idas.app.utils.LocationHelper.getLastKnownLocation(context)
            userLat = loc?.latitude ?: 52.3759
            userLon = loc?.longitude ?: 9.7320
        } else {
            userLat = 52.3759; userLon = 9.7320 // Hannover fallback
        }
    }

    LaunchedEffect(Unit) {
        val loc = com.idas.app.utils.LocationHelper.getLastKnownLocation(context)
        if (loc != null) {
            userLat = loc.latitude; userLon = loc.longitude
        } else {
            permLauncher.launch(arrayOf(
                Manifest.permission.ACCESS_FINE_LOCATION,
                Manifest.permission.ACCESS_COARSE_LOCATION
            ))
            kotlinx.coroutines.delay(500)
            if (userLat == null) { userLat = 52.3759; userLon = 9.7320 }
        }
    }

    LaunchedEffect(symptomIds) {
        try {
            val res = ApiService.getMatching(token, symptomIds)
            if (res.getBoolean("success")) {
                val arr = res.getJSONArray("ergebnisse")
                results = (0 until arr.length()).map { i ->
                    val fb     = arr.getJSONObject(i)
                    val aerzte = fb.getJSONArray("aerzte")
                    FachbereichResult(
                        fachbereichId = fb.optInt("fachbereich_id", 0),
                        fachbereich   = fb.getString("fachbereich"),
                        punkte        = fb.getInt("punkte"),
                        aerzte        = (0 until aerzte.length()).map { j ->
                            val a = aerzte.getJSONObject(j)
                            Arzt(a.getInt("arzt_id"), a.getString("name"),
                                a.optString("telefon",""), a.optString("email",""),
                                a.optString("addresse",""))
                        }
                    )
                }
            } else error = res.optString("message", "Keine Ergebnisse.")
        } catch (e: Exception) { error = Strings.get("error_server") }
        finally { loading = false }
    }

    val filteredResults = if (searchQuery.isBlank()) results else {
        results.map { fb ->
            fb.copy(aerzte = fb.aerzte.filter {
                it.name.contains(searchQuery, ignoreCase = true) ||
                        fb.fachbereich.contains(searchQuery, ignoreCase = true)
            })
        }.filter { it.aerzte.isNotEmpty() }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(Strings.get("matching_title")) },
                navigationIcon = {
                    IconButton(onClick = {
                        if (showSearch) { showSearch = false; searchQuery = "" }
                        else onBack()
                    }) { Icon(Icons.Default.ArrowBack, Strings.get("back")) }
                },
                actions = {
                    IconButton(onClick = {
                        showSearch = !showSearch
                        if (!showSearch) searchQuery = ""
                    }) {
                        Icon(
                            if (showSearch) Icons.Default.Close else Icons.Default.Search,
                            "Suchen", tint = Color.White
                        )
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = IDASBlue,
                    titleContentColor = Color.White,
                    navigationIconContentColor = Color.White
                )
            )
        }
    ) { padding ->
        Column(modifier = Modifier.fillMaxSize().background(bg).padding(padding)) {

            // Search bar
            AnimatedVisibility(
                visible = showSearch,
                enter = expandVertically() + fadeIn(),
                exit  = shrinkVertically() + fadeOut()
            ) {
                OutlinedTextField(
                    value = searchQuery,
                    onValueChange = { searchQuery = it },
                    placeholder = { Text(
                        if (Strings.language == "de") "Arzt oder Fachbereich suchen…"
                        else "Search doctor or specialty…"
                    )},
                    leadingIcon = { Icon(Icons.Default.Search, null, tint = IDASBlue) },
                    trailingIcon = {
                        if (searchQuery.isNotEmpty()) {
                            IconButton(onClick = { searchQuery = "" }) {
                                Icon(Icons.Default.Close, null, tint = textSec)
                            }
                        }
                    },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                        .padding(horizontal = 16.dp, vertical = 10.dp),
                    shape = RoundedCornerShape(14.dp),
                    colors = OutlinedTextFieldDefaults.colors(
                        focusedBorderColor = IDASBlue,
                        focusedContainerColor = Color.White,
                        unfocusedContainerColor = Color.White
                    )
                )
            }

            when {
                loading -> Box(Modifier.fillMaxSize(), Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        CircularProgressIndicator(color = IDASBlue, strokeWidth = 3.dp)
                        Spacer(Modifier.height(16.dp))
                        Text(Strings.get("matching_loading"),
                            color = textSec, fontSize = 15.sp)
                    }
                }
                error.isNotEmpty() -> Box(Modifier.fillMaxSize(), Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text("😕", fontSize = 48.sp)
                        Spacer(Modifier.height(12.dp))
                        Text(error, color = IDASRed, fontSize = 15.sp)
                    }
                }
                else -> LazyColumn(
                    modifier = Modifier.fillMaxSize(),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(20.dp)
                ) {
                    // Header
                    item {
                        Surface(
                            color = if (searchQuery.isNotBlank()) IDASBlueLight else IDASBlueGray,
                            shape = RoundedCornerShape(14.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Row(modifier = Modifier.padding(14.dp),
                                verticalAlignment = Alignment.CenterVertically) {
                                Text(if (searchQuery.isNotBlank()) "🔍" else "🎯", fontSize = 20.sp)
                                Spacer(Modifier.width(10.dp))
                                Text(
                                    if (searchQuery.isNotBlank())
                                        "${filteredResults.sumOf { it.aerzte.size }} ${if (Strings.language == "de") "Ärzte gefunden" else "doctors found"}"
                                    else Strings.get("matching_based"),
                                    fontSize = 14.sp, color = textPri,
                                    fontWeight = FontWeight.Medium
                                )
                            }
                        }
                    }

                    if (filteredResults.isEmpty() && searchQuery.isNotBlank()) {
                        item {
                            Box(Modifier.fillMaxWidth().padding(40.dp),
                                contentAlignment = Alignment.Center) {
                                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                    Text("🔍", fontSize = 48.sp)
                                    Spacer(Modifier.height(12.dp))
                                    Text(
                                        if (Strings.language == "de")
                                            "Keine Ergebnisse für \"$searchQuery\""
                                        else "No results for \"$searchQuery\"",
                                        color = textSec, fontSize = 15.sp
                                    )
                                }
                            }
                        }
                    }

                    items(filteredResults.size) { index ->
                        val result = filteredResults[index]
                        val configs = listOf(
                            Triple(Color(0xFF1565C0), "🥇", if (Strings.language == "de") "Beste Empfehlung" else "Best Match"),
                            Triple(Color(0xFF2E7D32), "🥈", if (Strings.language == "de") "Sehr empfohlen" else "Highly Recommended"),
                            Triple(Color(0xFF6A1B9A), "🥉", if (Strings.language == "de") "Empfohlen" else "Recommended")
                        )
                        val origIdx = results.indexOfFirst { it.fachbereich == result.fachbereich }
                        val (rankColor, medal, rankLabel) = configs.getOrElse(origIdx) {
                            Triple(IDASBlue, "⭐", if (Strings.language == "de") "Empfohlen" else "Recommended")
                        }

                        val visibleCount = remember(result.fachbereich) { mutableStateOf(3) }
                        val showAllDocs  = remember(result.fachbereich) { mutableStateOf(false) }

                        // Sort by city coordinates — instant, no API calls, no DB changes
                        val sortedDoctors = remember(result.fachbereich, userLat, userLon) {
                            val lat = userLat; val lon = userLon
                            if (lat == null || lon == null) result.aerzte
                            else result.aerzte.sortedBy { arzt ->
                                val coords = getCoordsFromAddress(arzt.addresse)
                                if (coords != null)
                                    com.idas.app.utils.LocationHelper.distanceKm(
                                        lat, lon, coords.first, coords.second)
                                else Double.MAX_VALUE
                            }
                        }
                        val total     = sortedDoctors.size
                        val displayed = if (showAllDocs.value) sortedDoctors
                        else sortedDoctors.take(visibleCount.value)

                        Card(
                            shape = RoundedCornerShape(24.dp),
                            elevation = CardDefaults.cardElevation(4.dp),
                            colors = CardDefaults.cardColors(containerColor = cardColor),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            Column {
                                // Header
                                Box(
                                    modifier = Modifier.fillMaxWidth()
                                        .background(Brush.horizontalGradient(
                                            listOf(rankColor, rankColor.copy(0.75f))))
                                        .padding(18.dp)
                                ) {
                                    Row(verticalAlignment = Alignment.CenterVertically) {
                                        Box(
                                            modifier = Modifier.size(48.dp)
                                                .clip(RoundedCornerShape(14.dp))
                                                .background(Color.White.copy(0.2f)),
                                            contentAlignment = Alignment.Center
                                        ) { Text(medal, fontSize = 24.sp) }
                                        Spacer(Modifier.width(14.dp))
                                        Column(modifier = Modifier.weight(1f)) {
                                            Text(result.fachbereich, fontSize = 18.sp,
                                                fontWeight = FontWeight.Bold, color = Color.White)
                                            Text(rankLabel, fontSize = 12.sp,
                                                color = Color.White.copy(0.8f))
                                        }
                                        Surface(color = Color.White.copy(0.25f),
                                            shape = RoundedCornerShape(20.dp)) {
                                            Row(modifier = Modifier.padding(
                                                horizontal = 10.dp, vertical = 5.dp),
                                                verticalAlignment = Alignment.CenterVertically) {
                                                Icon(Icons.Default.Star, null,
                                                    tint = Color.White,
                                                    modifier = Modifier.size(14.dp))
                                                Spacer(Modifier.width(3.dp))
                                                Text("${result.punkte} Pkt", fontSize = 12.sp,
                                                    color = Color.White, fontWeight = FontWeight.Bold)
                                            }
                                        }
                                    }
                                }

                                // Doctor count
                                Surface(color = rankColor.copy(0.06f),
                                    modifier = Modifier.fillMaxWidth()) {
                                    Row(modifier = Modifier.padding(
                                        horizontal = 18.dp, vertical = 10.dp),
                                        verticalAlignment = Alignment.CenterVertically) {
                                        Icon(Icons.Default.Person, null,
                                            tint = rankColor, modifier = Modifier.size(16.dp))
                                        Spacer(Modifier.width(6.dp))
                                        Text(
                                            "$total ${if (Strings.language == "de") "Ärzte verfügbar" else "doctors available"}",
                                            fontSize = 13.sp, color = rankColor,
                                            fontWeight = FontWeight.SemiBold
                                        )
                                    }
                                }

                                // Doctor cards
                                Column(modifier = Modifier.padding(12.dp),
                                    verticalArrangement = Arrangement.spacedBy(10.dp)) {
                                    displayed.forEach { arzt ->
                                        val coords = getCoordsFromAddress(arzt.addresse)
                                        val dist = if (userLat != null && coords != null)
                                            com.idas.app.utils.LocationHelper.distanceKm(
                                                userLat!!, userLon!!, coords.first, coords.second)
                                        else null
                                        ArztCard(
                                            arzt        = arzt,
                                            fachbereich = result.fachbereich,
                                            accentColor = rankColor,
                                            distanceKm  = dist,
                                            onBook      = onBook,
                                            onDetail    = { onDoctorDetail(arzt, result.fachbereich) }
                                        )
                                    }

                                    // Expand / collapse
                                    if (total > 3) {
                                        val remaining = total - displayed.size
                                        if (!showAllDocs.value && remaining > 0) {
                                            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                                OutlinedButton(
                                                    onClick = { visibleCount.value += 5 },
                                                    modifier = Modifier.weight(1f).height(40.dp),
                                                    shape = RoundedCornerShape(12.dp),
                                                    colors = ButtonDefaults.outlinedButtonColors(
                                                        contentColor = rankColor)
                                                ) {
                                                    Icon(Icons.Default.ExpandMore, null,
                                                        modifier = Modifier.size(16.dp))
                                                    Spacer(Modifier.width(4.dp))
                                                    Text(
                                                        "+ ${minOf(5, remaining)} ${if (Strings.language == "de") "mehr" else "more"}",
                                                        fontSize = 13.sp, fontWeight = FontWeight.Bold)
                                                }
                                                Button(
                                                    onClick = { showAllDocs.value = true },
                                                    modifier = Modifier.weight(1f).height(40.dp),
                                                    shape = RoundedCornerShape(12.dp),
                                                    colors = ButtonDefaults.buttonColors(
                                                        containerColor = rankColor)
                                                ) {
                                                    Text(
                                                        if (Strings.language == "de") "Alle ($total)"
                                                        else "All ($total)",
                                                        fontSize = 13.sp, fontWeight = FontWeight.Bold)
                                                }
                                            }
                                        } else {
                                            TextButton(
                                                onClick = {
                                                    showAllDocs.value = false
                                                    visibleCount.value = 3
                                                },
                                                modifier = Modifier.fillMaxWidth()
                                            ) {
                                                Icon(Icons.Default.ExpandLess, null,
                                                    tint = rankColor,
                                                    modifier = Modifier.size(16.dp))
                                                Spacer(Modifier.width(4.dp))
                                                Text(
                                                    if (Strings.language == "de") "Weniger anzeigen"
                                                    else "Show less",
                                                    color = rankColor, fontSize = 13.sp,
                                                    fontWeight = FontWeight.Bold)
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    item { Spacer(Modifier.height(8.dp)) }
                }
            }
        }
    }
}

@Composable
fun ArztCard(
    arzt: Arzt, fachbereich: String, accentColor: Color,
    distanceKm: Double? = null,
    onBook: (Int, String, String) -> Unit, onDetail: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Card(
        shape = RoundedCornerShape(16.dp),
        elevation = CardDefaults.cardElevation(2.dp),
        colors = CardDefaults.cardColors(containerColor = cardColor),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(14.dp)) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier.size(50.dp).clip(RoundedCornerShape(14.dp))
                        .background(accentColor.copy(0.15f)),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        arzt.name.split(" ").filter { it.isNotBlank() }
                            .mapNotNull { it.firstOrNull()?.uppercaseChar() }
                            .take(2).joinToString(""),
                        fontWeight = FontWeight.Bold, color = accentColor, fontSize = 16.sp
                    )
                }
                Spacer(Modifier.width(14.dp))
                Column(modifier = Modifier.weight(1f)) {
                    Text(arzt.name, fontWeight = FontWeight.Bold,
                        fontSize = 15.sp, color = textPri)
                    if (distanceKm != null) {
                        Surface(color = IDASGreen.copy(0.12f),
                            shape = RoundedCornerShape(20.dp),
                            modifier = Modifier.padding(top = 3.dp)) {
                            Text("📍 ${"%.0f".format(distanceKm)} km",
                                modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp),
                                fontSize = 12.sp, color = IDASGreenDark,
                                fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }

            if (arzt.addresse.isNotBlank() || arzt.telefon.isNotBlank()) {
                Spacer(Modifier.height(10.dp))
                HorizontalDivider(color = border, thickness = 0.5.dp)
                Spacer(Modifier.height(8.dp))
                if (arzt.addresse.isNotBlank()) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Default.LocationOn, null, tint = textSec,
                            modifier = Modifier.size(14.dp))
                        Spacer(Modifier.width(5.dp))
                        Text(arzt.addresse, fontSize = 12.sp, color = textSec,
                            modifier = Modifier.weight(1f))
                    }
                }
                if (arzt.telefon.isNotBlank()) {
                    Spacer(Modifier.height(3.dp))
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(Icons.Default.Phone, null, tint = textSec,
                            modifier = Modifier.size(14.dp))
                        Spacer(Modifier.width(5.dp))
                        Text(arzt.telefon, fontSize = 12.sp, color = textSec)
                    }
                }
            }

            Spacer(Modifier.height(12.dp))
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedButton(
                    onClick = onDetail,
                    modifier = Modifier.weight(1f).height(40.dp),
                    shape = RoundedCornerShape(12.dp),
                    colors = ButtonDefaults.outlinedButtonColors(contentColor = accentColor)
                ) {
                    Text(if (Strings.language == "de") "Details" else "Details",
                        fontSize = 13.sp, fontWeight = FontWeight.Bold)
                }
                Button(
                    onClick = { onBook(arzt.arztId, arzt.name, fachbereich) },
                    modifier = Modifier.weight(1f).height(40.dp),
                    shape = RoundedCornerShape(12.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = accentColor)
                ) {
                    Text(Strings.get("matching_book"),
                        fontSize = 13.sp, fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}