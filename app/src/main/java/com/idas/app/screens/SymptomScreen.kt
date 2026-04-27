package com.idas.app.screens

import android.graphics.Typeface
import android.graphics.Paint as AndroidPaint
import androidx.compose.animation.*
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.gestures.detectTapGestures
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Check
import androidx.compose.material.icons.filled.List
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.graphics.drawscope.DrawScope
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.graphics.drawscope.drawIntoCanvas
import androidx.compose.ui.graphics.nativeCanvas
import androidx.compose.ui.input.pointer.pointerInput
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.idas.app.models.Symptom
import com.idas.app.network.ApiService
import com.idas.app.ui.theme.*
import com.idas.app.utils.Strings

//  Colours
private val SkinBase    = Color(0xFFFDE9D9)
private val SkinStroke  = Color(0xFFE8C4A0)
private val ActiveFill  = Color(0xFF2980B9).copy(alpha = 0.30f)
private val SelectedFill   = Color(0xFFE74C3C).copy(alpha = 0.25f)
private val SelectedStroke = Color(0xFFE74C3C)

//Body zones
enum class BodyZone(val label: String, val emoji: String, val symptomNames: List<String>) {
    HEAD("Kopf & Hals", "🧠", listOf(
        "Kopfschmerzen","Schwindel","Gedächtnisverlust","Verwirrung",
        "Halsschmerzen","Laufende Nase","Nasenverstopfung","Hörverlust",
        "Ohrenschmerzen","Zahnweh","Verschwommenes Sehen","Krampfanfälle"
    )),
    CHEST("Brust & Herz", "❤️", listOf(
        "Brustschmerzen","Herzrasen","Atemnot","Husten"
    )),
    ABDOMEN("Bauch & Magen", "🫁", listOf(
        "Bauchschmerzen","Erbrechen","Übelkeit","Durchfall",
        "Verstopfung","Bauchblähungen","Sodbrennen","Gelbsucht"
    )),
    ARMS("Arme & Hände", "💪", listOf(
        "Taubheitsgefühl","Muskelkraftverlust","Schwellung",
        "Zittern","Muskelschmerzen","Gelenkschmerzen"
    )),
    LEGS("Beine & Füße", "🦵", listOf(
        "Schwellung","Muskelschmerzen","Gelenkschmerzen",
        "Taubheitsgefühl","Muskelkraftverlust","Rückenschmerzen"
    )),
    SKIN("Haut & Allgemein", "🩺", listOf(
        "Hautausschlag","Juckreiz","Haarausfall","Spröde Nägel",
        "Fieber","Schüttelfrost","Müdigkeit","Nachtschweiß",
        "Unerklärter Gewichtsverlust","Unerklärte Gewichtszunahme",
        "Leichte Blutergüsse","Blutende Zahnfleisch"
    )),
    MENTAL("Psyche & Geist", "💭", listOf(
        "Schlaflosigkeit","Depression","Angst","Gedächtnisverlust","Verwirrung"
    )),
    URINARY("Niere & Blase", "💧", listOf(
        "Häufiges Wasserlassen","Starker Durst","Trockener Mund","Dunkler Urin"
    ))
}

@OptIn(ExperimentalMaterial3Api::class, ExperimentalAnimationApi::class)
@Composable
fun SymptomScreen(
    token: String,
    onBack: () -> Unit,
    onResults: (String) -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    var symptoms  by remember { mutableStateOf<List<Symptom>>(emptyList()) }
    var selected  by remember { mutableStateOf<Set<Int>>(emptySet()) }
    var loading   by remember { mutableStateOf(true) }
    var error     by remember { mutableStateOf("") }
    var activeZone by remember { mutableStateOf<BodyZone?>(null) }
    var showAll   by remember { mutableStateOf(false) }
    var search    by remember { mutableStateOf("") }

    LaunchedEffect(Unit) {
        try {
            val res = ApiService.getSymptome(token)
            if (res.getBoolean("success")) {
                val arr = res.getJSONArray("data")
                symptoms = (0 until arr.length()).map {
                    val o = arr.getJSONObject(it)
                    Symptom(o.getInt("id"), o.getString("name"))
                }
            } else error = "Symptome konnten nicht geladen werden."
        } catch (e: Exception) { error = "Server nicht erreichbar." }
        finally { loading = false }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text(Strings.get("symptom_title")) },
                navigationIcon = {
                    IconButton(onClick = onBack) { Icon(Icons.Default.ArrowBack, "Zurück") }
                },
                actions = {
                    Surface(
                        onClick = { showAll = !showAll },
                        color = Color.White.copy(0.2f),
                        shape = RoundedCornerShape(20.dp),
                        modifier = Modifier.padding(end = 8.dp)
                    ) {
                        Row(modifier = Modifier.padding(horizontal = 14.dp, vertical = 7.dp),
                            verticalAlignment = Alignment.CenterVertically) {
                            Icon(
                                if (showAll) Icons.Default.Person else Icons.Default.List,
                                null, tint = Color.White,
                                modifier = Modifier.size(16.dp)
                            )
                            Spacer(Modifier.width(5.dp))
                            Text(
                                if (showAll) Strings.get("symptom_body") else Strings.get("symptom_all"),
                                color = Color.White, fontSize = 13.sp, fontWeight = FontWeight.Bold
                            )
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(
                    containerColor = IDASBlue, titleContentColor = Color.White,
                    navigationIconContentColor = Color.White)
            )
        },
        bottomBar = {
            Surface(shadowElevation = 8.dp, color = cardColor) {
                Column(modifier = Modifier.padding(horizontal = 16.dp, vertical = 12.dp)) {
                    if (selected.isNotEmpty()) {
                        val names = symptoms.filter { it.id in selected }
                        Row(modifier = Modifier.fillMaxWidth().padding(bottom = 8.dp),
                            horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                            names.take(3).forEach { s ->
                                Surface(color = IDASBlue, shape = RoundedCornerShape(20.dp)) {
                                    Row(verticalAlignment = Alignment.CenterVertically,
                                        modifier = Modifier.padding(start = 8.dp, end = 4.dp, top = 4.dp, bottom = 4.dp)) {
                                        Text(s.name, fontSize = 11.sp, color = Color.White, maxLines = 1)
                                        Spacer(Modifier.width(2.dp))
                                        Box(modifier = Modifier.size(16.dp).clip(CircleShape)
                                            .background(Color.White.copy(0.3f)).clickable { selected = selected - s.id },
                                            contentAlignment = Alignment.Center) {
                                            Text("×", fontSize = 11.sp, color = Color.White, fontWeight = FontWeight.Bold)
                                        }
                                    }
                                }
                            }
                            if (names.size > 3) {
                                Surface(color = IDASBlueDark, shape = RoundedCornerShape(20.dp)) {
                                    Text("+${names.size - 3}",
                                        modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp),
                                        fontSize = 11.sp, color = Color.White, fontWeight = FontWeight.Bold)
                                }
                            }
                        }
                    }
                    Button(
                        onClick = { if (selected.isNotEmpty()) onResults(selected.joinToString(",")) },
                        modifier = Modifier.fillMaxWidth().height(48.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = IDASBlue),
                        shape = RoundedCornerShape(8.dp), enabled = selected.isNotEmpty()
                    ) {
                        Text(
                            if (selected.isEmpty()) Strings.get("symptom_select")
                            else "${Strings.get("symptom_button")} (${selected.size} ${Strings.get("symptom_count")}) →",
                            fontSize = 14.sp, fontWeight = FontWeight.Bold
                        )
                    }
                }
            }
        }
    ) { padding ->
        Box(modifier = Modifier.fillMaxSize().background(bg).padding(padding)) {
            when {
                loading -> Box(Modifier.fillMaxSize(), Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        CircularProgressIndicator(color = IDASBlue)
                        Spacer(Modifier.height(12.dp))
                        Text("Lade Symptome…", color = Color.Gray, fontSize = 13.sp)
                    }
                }
                error.isNotEmpty() -> Box(Modifier.fillMaxSize(), Alignment.Center) {
                    Text(error, color = Color.Red)
                }
                showAll -> AllSymptomsView(symptoms, selected, search,
                    onSearchChange = { search = it },
                    onToggle = { id -> selected = if (id in selected) selected - id else selected + id })
                else -> BodyMapView(
                    symptoms = symptoms, selected = selected, activeZone = activeZone,
                    onZoneTapped = { zone -> activeZone = if (activeZone == zone) null else zone },
                    onToggle = { id -> selected = if (id in selected) selected - id else selected + id },
                    onClosePanel = { activeZone = null }
                )
            }
        }
    }
}

//  Body Map View
@OptIn(ExperimentalAnimationApi::class)
@Composable
fun BodyMapView(
    symptoms: List<Symptom>,
    selected: Set<Int>,
    activeZone: BodyZone?,
    onZoneTapped: (BodyZone) -> Unit,
    onToggle: (Int) -> Unit,
    onClosePanel: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    fun zoneHasSelected(zone: BodyZone): Boolean {
        val names = zone.symptomNames.map { it.lowercase() }
        return symptoms.any { it.id in selected && names.contains(it.name.lowercase()) }
    }

    Column(modifier = Modifier.fillMaxSize()) {
        Text(Strings.get("symptom_hint"), fontSize = 12.sp, color = Color.Gray,
            textAlign = TextAlign.Center,
            modifier = Modifier.fillMaxWidth().padding(top = 10.dp, bottom = 4.dp))

        Row(modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp),
            horizontalArrangement = Arrangement.Center, verticalAlignment = Alignment.CenterVertically) {
            LegendDot(color = IDASBlue.copy(0.3f), label = "Aktiv")
            Spacer(Modifier.width(16.dp))
            LegendDot(color = SelectedFill, label = "Ausgewählt")
        }

        //  Extra zone chips ABOVE the body map
        Row(
            modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp, vertical = 6.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            listOf(BodyZone.SKIN, BodyZone.MENTAL, BodyZone.URINARY).forEach { zone ->
                val isActive    = activeZone == zone
                val hasSelected = zoneHasSelected(zone)
                Surface(
                    onClick = { onZoneTapped(zone) },
                    color = when {
                        isActive    -> IDASBlue
                        hasSelected -> Color(0xFFE74C3C)
                        else        -> cardColor
                    },
                    shape = RoundedCornerShape(20.dp),
                    shadowElevation = 2.dp,
                    modifier = Modifier.height(34.dp)
                ) {
                    Row(modifier = Modifier.padding(horizontal = 12.dp),
                        verticalAlignment = Alignment.CenterVertically) {
                        Text(zone.emoji, fontSize = 14.sp)
                        Spacer(Modifier.width(5.dp))
                        Text(zone.label.split(" ")[0], fontSize = 12.sp,
                            color = if (isActive || hasSelected) Color.White else Color(0xFF555555),
                            fontWeight = FontWeight.Medium)
                    }
                }
            }
        }

        // Body + panel row
        Row(modifier = Modifier.fillMaxWidth().weight(1f).padding(horizontal = 8.dp),
            horizontalArrangement = Arrangement.Center) {

            // Body figure
            Box(modifier = Modifier.width(180.dp).fillMaxHeight()) {
                ImprovedBodyMap(
                    activeZone      = activeZone,
                    selectedSymptoms = selected,
                    symptoms        = symptoms,
                    onZoneTapped    = onZoneTapped,
                    zoneHasSelected = ::zoneHasSelected
                )
            }

            // Side panel
            AnimatedVisibility(
                visible = activeZone != null,
                enter   = slideInHorizontally { it } + fadeIn(),
                exit    = slideOutHorizontally { it } + fadeOut()
            ) {
                activeZone?.let { zone ->
                    val zoneSymptoms = symptoms.filter { s ->
                        zone.symptomNames.any { it.equals(s.name, ignoreCase = true) }
                    }
                    ZonePanel(zone, zoneSymptoms, selected, onToggle, onClosePanel)
                }
            }
        }
    }
}

@Composable
fun LegendDot(color: Color, label: String) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Row(verticalAlignment = Alignment.CenterVertically) {
        Box(modifier = Modifier.size(12.dp).clip(CircleShape).background(color))
        Spacer(Modifier.width(4.dp))
        Text(label, fontSize = 11.sp, color = Color.Gray)
    }
}

//Body Canvas
@Composable
fun ImprovedBodyMap(
    activeZone: BodyZone?,
    selectedSymptoms: Set<Int>,
    symptoms: List<Symptom>,
    onZoneTapped: (BodyZone) -> Unit,
    zoneHasSelected: (BodyZone) -> Boolean
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Canvas(modifier = Modifier.fillMaxSize().pointerInput(Unit) {
        detectTapGestures { offset ->
            val w  = size.width.toFloat()
            val h  = size.height.toFloat()
            val cx = w / 2f
            val headTop  = h * 0.01f; val headBot  = h * 0.17f
            val chestBot = h * 0.34f; val abdBot   = h * 0.51f
            val legsBot  = h * 0.96f
            val bL = cx - w * 0.24f; val bR = cx + w * 0.24f
            val aL = cx - w * 0.45f; val aR = cx + w * 0.45f
            when {
                offset.y in headTop..headBot && offset.x in (cx-w*0.21f)..(cx+w*0.21f)
                    -> onZoneTapped(BodyZone.HEAD)
                offset.y in headBot..chestBot && offset.x in bL..bR
                    -> onZoneTapped(BodyZone.CHEST)
                offset.y in chestBot..abdBot && offset.x in bL..bR
                    -> onZoneTapped(BodyZone.ABDOMEN)
                offset.y in headBot..abdBot && (offset.x in aL..bL || offset.x in bR..aR)
                    -> onZoneTapped(BodyZone.ARMS)
                offset.y in abdBot..legsBot && offset.x in bL..bR
                    -> onZoneTapped(BodyZone.LEGS)
            }
        }
    }) {
        val w  = size.width; val h  = size.height; val cx = w / 2f

        fun zoneFill(zone: BodyZone) = when {
            activeZone == zone    -> ActiveFill
            zoneHasSelected(zone) -> SelectedFill
            else -> SkinBase
        }
        fun zoneStroke(zone: BodyZone) = when {
            activeZone == zone    -> IDASBlue
            zoneHasSelected(zone) -> SelectedStroke
            else -> SkinStroke
        }
        val sw = Stroke(width = 2.5f)

        // Head
        val hR  = w * 0.19f; val hCY = h * 0.10f
        drawCircle(Color.Black.copy(0.07f), hR + 3f, Offset(cx + 2f, hCY + 3f))
        drawCircle(SkinBase, hR, Offset(cx, hCY))
        drawCircle(zoneFill(BodyZone.HEAD), hR, Offset(cx, hCY))
        drawCircle(zoneStroke(BodyZone.HEAD), hR, Offset(cx, hCY), style = sw)
        // Eyes
        val eyeY = hCY - hR * 0.12f
        drawCircle(Color(0xFF555555).copy(0.7f), 3.8f, Offset(cx - hR*0.33f, eyeY))
        drawCircle(Color(0xFF555555).copy(0.7f), 3.8f, Offset(cx + hR*0.33f, eyeY))
        // Eyebrows
        val browY = eyeY - 6f
        drawLine(Color(0xFF555555).copy(0.5f), Offset(cx-hR*0.43f,browY), Offset(cx-hR*0.22f,browY-2f), 2f)
        drawLine(Color(0xFF555555).copy(0.5f), Offset(cx+hR*0.22f,browY-2f), Offset(cx+hR*0.43f,browY), 2f)
        // Nose
        drawLine(Color(0xFF888888).copy(0.5f), Offset(cx,eyeY+4f), Offset(cx-3f,eyeY+11f), 1.5f)
        drawLine(Color(0xFF888888).copy(0.5f), Offset(cx-3f,eyeY+11f), Offset(cx+3f,eyeY+11f), 1.5f)
        // Smile
        val mouthPath = Path().apply {
            moveTo(cx-7f, hCY+hR*0.30f)
            cubicTo(cx-3f,hCY+hR*0.37f, cx+3f,hCY+hR*0.37f, cx+7f,hCY+hR*0.30f)
        }
        drawPath(mouthPath, Color(0xFF888888).copy(0.6f), style = Stroke(1.5f))

        // Neck
        val nW = w*0.10f; val nT = hCY+hR-4f; val nB = h*0.19f
        drawRect(SkinBase,   Offset(cx-nW,nT), Size(nW*2,nB-nT))
        drawRect(SkinStroke, Offset(cx-nW,nT), Size(nW*2,nB-nT), style = sw)

        //Torso
        val tL = cx-w*0.23f; val tR = cx+w*0.23f
        val tT = nB; val cB = h*0.345f; val aB = h*0.510f

        // Chest
        val chestPath = Path().apply {
            moveTo(tL-5f, tT); lineTo(tR+5f, tT); lineTo(tR, cB); lineTo(tL, cB); close()
        }
        drawPath(chestPath, SkinBase)
        drawPath(chestPath, zoneFill(BodyZone.CHEST))
        drawPath(chestPath, zoneStroke(BodyZone.CHEST), style = sw)
        drawLine(SkinStroke.copy(0.3f), Offset(cx,tT+8f), Offset(cx,cB-8f), 1.2f)
        drawLine(SkinStroke.copy(0.3f), Offset(cx,tT+4f), Offset(tL+8f,tT+10f), 1.2f)
        drawLine(SkinStroke.copy(0.3f), Offset(cx,tT+4f), Offset(tR-8f,tT+10f), 1.2f)

        // Abdomen
        drawRect(SkinBase,                  Offset(tL,cB), Size(tR-tL, aB-cB))
        drawRect(zoneFill(BodyZone.ABDOMEN),Offset(tL,cB), Size(tR-tL, aB-cB))
        drawRect(zoneStroke(BodyZone.ABDOMEN),Offset(tL,cB), Size(tR-tL, aB-cB), style = sw)
        drawCircle(SkinStroke.copy(0.4f), 3f, Offset(cx, (cB+aB)/2f))

        //  Arms
        val armW = w*0.11f; val armT = tT; val armBB = h*0.535f
        val armFill   = zoneFill(BodyZone.ARMS)
        val armStroke = zoneStroke(BodyZone.ARMS)

        val leftArmPath = Path().apply {
            moveTo(tL,armT); lineTo(tL-armW,armT+(armBB-armT)*0.15f)
            lineTo(tL-armW+4f,armBB); lineTo(tL,armBB); close()
        }
        drawPath(leftArmPath, SkinBase); drawPath(leftArmPath, armFill); drawPath(leftArmPath, armStroke, style=sw)

        val rightArmPath = Path().apply {
            moveTo(tR,armT); lineTo(tR+armW,armT+(armBB-armT)*0.15f)
            lineTo(tR+armW-4f,armBB); lineTo(tR,armBB); close()
        }
        drawPath(rightArmPath, SkinBase); drawPath(rightArmPath, armFill); drawPath(rightArmPath, armStroke, style=sw)

        // Legs
        val legW = w*0.138f; val legT = aB; val legBB = h*0.965f; val lgap = w*0.03f
        val legFill   = zoneFill(BodyZone.LEGS)
        val legStroke = zoneStroke(BodyZone.LEGS)

        val lLegPath = Path().apply {
            moveTo(cx-lgap,legT); lineTo(cx-lgap-legW,legT+(legBB-legT)*0.05f)
            lineTo(cx-lgap-legW+5f,legBB); lineTo(cx-lgap-5f,legBB); close()
        }
        drawPath(lLegPath, SkinBase); drawPath(lLegPath, legFill); drawPath(lLegPath, legStroke, style=sw)

        val rLegPath = Path().apply {
            moveTo(cx+lgap,legT); lineTo(cx+lgap+legW,legT+(legBB-legT)*0.05f)
            lineTo(cx+lgap+legW-5f,legBB); lineTo(cx+lgap+5f,legBB); close()
        }
        drawPath(rLegPath, SkinBase); drawPath(rLegPath, legFill); drawPath(rLegPath, legStroke, style=sw)

        // Labels
        drawBodyLabel("Kopf",  cx,                      hCY+hR*0.55f, 20f)
        drawBodyLabel("Brust", cx,                      (tT+cB)/2f+6f, 18f)
        drawBodyLabel("Bauch", cx,                      (cB+aB)/2f+6f, 18f)
        drawBodyLabel("Arm",   tL-armW*0.35f-6f,        (armT+armBB)/2f, 14f)
        drawBodyLabel("Arm",   tR+armW*0.35f+6f,        (armT+armBB)/2f, 14f)
        drawBodyLabel("Bein",  cx-lgap-legW*0.4f-6f,    (legT+legBB)/2f, 14f)
        drawBodyLabel("Bein",  cx+lgap+legW*0.4f+6f,    (legT+legBB)/2f, 14f)
    }
}

private fun DrawScope.drawBodyLabel(text: String, x: Float, y: Float, textSizePx: Float = 18f) {
    drawIntoCanvas { canvas ->
        val paint = AndroidPaint().apply {
            color = android.graphics.Color.argb(180, 26, 82, 118)
            textSize = textSizePx; textAlign = AndroidPaint.Align.CENTER
            typeface = Typeface.DEFAULT_BOLD; isAntiAlias = true
        }
        canvas.nativeCanvas.drawText(text, x, y, paint)
    }
}

// Zone panel
@Composable
fun ZonePanel(
    zone: BodyZone, zoneSymptoms: List<Symptom>,
    selected: Set<Int>, onToggle: (Int) -> Unit, onClose: () -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    Column(modifier = Modifier.width(175.dp).fillMaxHeight()
        .shadow(4.dp, RoundedCornerShape(14.dp)).clip(RoundedCornerShape(14.dp))
        .background(cardColor)) {
        Row(modifier = Modifier.fillMaxWidth()
            .background(Brush.horizontalGradient(listOf(IDASBlue, IDASBlueDark))).padding(10.dp),
            verticalAlignment = Alignment.CenterVertically) {
            Text(zone.emoji, fontSize = 16.sp); Spacer(Modifier.width(6.dp))
            Text(zone.label, color = Color.White, fontWeight = FontWeight.Bold,
                fontSize = 12.sp, modifier = Modifier.weight(1f))
            IconButton(onClick = onClose, modifier = Modifier.size(28.dp)) {
                Icon(Icons.Default.Close, "Schließen", tint = Color.White, modifier = Modifier.size(16.dp))
            }
        }
        val checkedCount = zoneSymptoms.count { it.id in selected }
        if (checkedCount > 0) {
            Surface(color = IDASBlue.copy(0.1f), modifier = Modifier.fillMaxWidth()) {
                Text("$checkedCount ausgewählt",
                    modifier = Modifier.padding(horizontal = 12.dp, vertical = 5.dp),
                    fontSize = 11.sp, color = IDASBlue, fontWeight = FontWeight.Bold)
            }
        }
        if (zoneSymptoms.isEmpty()) {
            Box(Modifier.fillMaxSize(), Alignment.Center) {
                Text("Keine Symptome\nverfügbar", color = Color.Gray, fontSize = 12.sp, textAlign = TextAlign.Center)
            }
        } else {
            LazyColumn {
                items(zoneSymptoms) { symptom ->
                    val isChecked = symptom.id in selected
                    Row(modifier = Modifier.fillMaxWidth().clickable { onToggle(symptom.id) }
                        .background(if (isChecked) IDASBlueLight else cardColor)
                        .padding(horizontal = 10.dp, vertical = 10.dp),
                        verticalAlignment = Alignment.CenterVertically) {
                        Checkbox(checked = isChecked, onCheckedChange = { onToggle(symptom.id) },
                            colors = CheckboxDefaults.colors(checkedColor = IDASBlue),
                            modifier = Modifier.size(20.dp))
                        Spacer(Modifier.width(8.dp))
                        Text(symptom.name, fontSize = 12.sp,
                            fontWeight = if (isChecked) FontWeight.SemiBold else FontWeight.Normal,
                            color = if (isChecked) IDASBlueDark else textPri, lineHeight = 16.sp)
                    }
                    Divider(color = border, thickness = 0.5.dp)
                }
            }
        }
    }
}

// All symptoms list
@Composable
fun AllSymptomsView(
    symptoms: List<Symptom>, selected: Set<Int>,
    search: String, onSearchChange: (String) -> Unit, onToggle: (Int) -> Unit
) {
    val bg        = IDASBackground
    val cardColor = IDASCard
    val textPri   = IDASTextPrimary
    val textSec   = IDASTextSecondary
    val border    = IDASBorder
    val blueGray  = IDASBlueGray

    val filtered = symptoms.filter { it.name.contains(search, ignoreCase = true) }
    val selectedCount = selected.size

    Column(modifier = Modifier.fillMaxSize().background(bg)) {

        // Search bar
        OutlinedTextField(
            value = search, onValueChange = onSearchChange,
            placeholder = { Text(Strings.get("symptom_search"), fontSize = 15.sp) },
            leadingIcon = { Icon(Icons.Default.Search, null, tint = IDASBlue) },
            trailingIcon = {
                if (search.isNotEmpty()) {
                    IconButton(onClick = { onSearchChange("") }) {
                        Icon(Icons.Default.Close, null, tint = IDASTextSecondary)
                    }
                }
            },
            modifier = Modifier.fillMaxWidth().padding(12.dp),
            shape = RoundedCornerShape(14.dp), singleLine = true,
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = IDASBlue,
                unfocusedBorderColor = IDASBorder,
                focusedContainerColor = cardColor,
                unfocusedContainerColor = cardColor
            )
        )

        // Stats + select all row
        Row(modifier = Modifier.fillMaxWidth()
            .padding(horizontal = 16.dp, vertical = 4.dp),
            verticalAlignment = Alignment.CenterVertically) {
            // Count badge
            Surface(color = if (selectedCount > 0) IDASBlue else IDASBlueGray,
                shape = RoundedCornerShape(20.dp)) {
                Text(
                    if (selectedCount > 0) "$selectedCount ausgewählt"
                    else "${filtered.size} Symptome",
                    modifier = Modifier.padding(horizontal = 12.dp, vertical = 5.dp),
                    fontSize = 13.sp,
                    color = if (selectedCount > 0) Color.White else IDASTextSecondary,
                    fontWeight = FontWeight.Bold
                )
            }
            Spacer(Modifier.weight(1f))
            if (selectedCount > 0) {
                TextButton(onClick = { selected.toList().forEach { onToggle(it) } }) {
                    Text(if (Strings.language == "de") "Alle abwählen" else "Deselect all",
                        color = IDASRed, fontSize = 13.sp, fontWeight = FontWeight.Bold)
                }
            } else {
                TextButton(onClick = { filtered.forEach { onToggle(it.id) } }) {
                    Text(if (Strings.language == "de") "Alle wählen" else "Select all",
                        color = IDASBlue, fontSize = 13.sp, fontWeight = FontWeight.Bold)
                }
            }
        }

        if (filtered.isEmpty()) {
            Box(Modifier.fillMaxSize(), Alignment.Center) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text("🔍", fontSize = 40.sp)
                    Spacer(Modifier.height(8.dp))
                    Text(if (Strings.language == "de") "Kein Symptom gefunden"
                    else "No symptom found",
                        color = IDASTextSecondary, fontSize = 15.sp)
                }
            }
        } else {
            LazyColumn(contentPadding = PaddingValues(horizontal = 12.dp, vertical = 4.dp),
                verticalArrangement = Arrangement.spacedBy(6.dp)) {
                items(filtered) { symptom ->
                    val isChecked = symptom.id in selected
                    Card(
                        modifier = Modifier.fillMaxWidth().clickable { onToggle(symptom.id) },
                        shape = RoundedCornerShape(14.dp),
                        colors = CardDefaults.cardColors(
                            containerColor = if (isChecked) IDASBlueLight else cardColor
                        ),
                        elevation = CardDefaults.cardElevation(
                            defaultElevation = if (isChecked) 0.dp else 1.dp
                        )
                    ) {
                        Row(
                            modifier = Modifier.padding(horizontal = 16.dp, vertical = 14.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            // Custom checkbox
                            Box(
                                modifier = Modifier.size(26.dp)
                                    .clip(RoundedCornerShape(8.dp))
                                    .background(if (isChecked) IDASBlue else border),
                                contentAlignment = Alignment.Center
                            ) {
                                if (isChecked) {
                                    Icon(Icons.Default.Check, null,
                                        tint = Color.White,
                                        modifier = Modifier.size(16.dp))
                                }
                            }
                            Spacer(Modifier.width(14.dp))
                            Text(
                                symptom.name,
                                fontSize = 15.sp,
                                fontWeight = if (isChecked) FontWeight.SemiBold else FontWeight.Normal,
                                color = if (isChecked) IDASBlueDark else IDASTextPrimary,
                                modifier = Modifier.weight(1f)
                            )
                            if (isChecked) {
                                Spacer(Modifier.width(8.dp))
                                Surface(color = IDASBlue, shape = RoundedCornerShape(20.dp)) {
                                    Text("✓", modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp),
                                        fontSize = 11.sp, color = Color.White, fontWeight = FontWeight.Bold)
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