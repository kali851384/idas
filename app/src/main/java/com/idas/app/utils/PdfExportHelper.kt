package com.idas.app.utils

import android.content.Context
import android.content.Intent
import android.graphics.Paint as AndroidPaint
import android.graphics.pdf.PdfDocument
import androidx.core.content.FileProvider
import com.idas.app.models.Termin
import java.io.File
import java.io.FileOutputStream
import java.text.SimpleDateFormat
import java.util.*

object PdfExportHelper {

    fun exportAppointmentHistory(context: Context, termine: List<Termin>, patientName: String) {
        try {
            val doc  = PdfDocument()
            val pageWidth  = 595
            val pageHeight = 842
            var pageNumber = 1
            var y = 0f

            fun newPage(): PdfDocument.Page {
                val info = PdfDocument.PageInfo.Builder(pageWidth, pageHeight, pageNumber++).create()
                return doc.startPage(info)
            }

            var currentPage = newPage()
            var canvas = currentPage.canvas

            val headerPaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(30, 111, 217)
            }
            val whitePaint = AndroidPaint().apply {
                color = android.graphics.Color.WHITE
                textSize = 18f; isFakeBoldText = true; isAntiAlias = true
            }
            val titlePaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(13, 27, 42)
                textSize = 16f; isFakeBoldText = true; isAntiAlias = true
            }
            val labelPaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(107, 122, 141)
                textSize = 10f; isFakeBoldText = true; isAntiAlias = true
            }
            val valuePaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(13, 27, 42)
                textSize = 12f; isAntiAlias = true
            }
            val smallPaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(150, 150, 150)
                textSize = 9f; isAntiAlias = true
            }
            val linePaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(226, 232, 240)
                strokeWidth = 1f
            }
            val greenPaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(0, 135, 90)
                textSize = 11f; isFakeBoldText = true; isAntiAlias = true
            }
            val grayPaint = AndroidPaint().apply {
                color = android.graphics.Color.rgb(107, 122, 141)
                textSize = 11f; isFakeBoldText = true; isAntiAlias = true
            }

            fun drawHeader() {
                canvas.drawRect(0f, 0f, pageWidth.toFloat(), 65f, headerPaint)
                canvas.drawText("IDAS — Terminverlauf", 24f, 30f, whitePaint)
                canvas.drawText(patientName, 24f, 52f, AndroidPaint().apply {
                    color = android.graphics.Color.WHITE; textSize = 12f
                    alpha = 200; isAntiAlias = true
                })
                val datePaint = AndroidPaint().apply {
                    color = android.graphics.Color.WHITE; textSize = 10f
                    alpha = 180; isAntiAlias = true
                    textAlign = AndroidPaint.Align.RIGHT
                }
                val dateStr = SimpleDateFormat("dd.MM.yyyy", Locale.GERMANY).format(Date())
                canvas.drawText("Exportiert: $dateStr", pageWidth - 24f, 44f, datePaint)
                y = 85f
            }

            fun checkNewPage(needed: Float) {
                if (y + needed > pageHeight - 40) {
                    canvas.drawLine(24f, pageHeight - 30f, pageWidth - 24f,
                        pageHeight - 30f, linePaint)
                    canvas.drawText("Seite ${pageNumber - 1}",
                        pageWidth / 2f, pageHeight - 15f,
                        AndroidPaint().apply {
                            color = android.graphics.Color.GRAY; textSize = 9f
                            textAlign = AndroidPaint.Align.CENTER; isAntiAlias = true
                        })
                    doc.finishPage(currentPage)
                    currentPage = newPage()
                    canvas = currentPage.canvas
                    drawHeader()
                }
            }

            drawHeader()

            // Summary stats
            val upcoming = termine.count { it.status == "Bevorstehend" }
            val past     = termine.count { it.status != "Bevorstehend" }

            canvas.drawRect(24f, y, pageWidth - 24f, y + 50f,
                AndroidPaint().apply { color = android.graphics.Color.rgb(238, 244, 255) })
            canvas.drawText("Gesamt: ${termine.size} Termine", 36f, y + 18f, titlePaint)
            canvas.drawText("✓ Abgeschlossen: $past", 36f, y + 36f, valuePaint)
            canvas.drawText("Bevorstehend: $upcoming",
                pageWidth / 2f, y + 36f, valuePaint)
            y += 66f

            // Appointments
            canvas.drawLine(24f, y, pageWidth - 24f, y, linePaint)
            y += 14f

            termine.forEachIndexed { i, termin ->
                checkNewPage(110f)

                // Card background
                val cardBg = if (termin.status == "Bevorstehend")
                    android.graphics.Color.rgb(232, 245, 233)
                else android.graphics.Color.rgb(248, 249, 250)

                canvas.drawRect(24f, y, pageWidth - 24f, y + 90f,
                    AndroidPaint().apply { color = cardBg })

                // Status stripe
                val stripeColor = if (termin.status == "Bevorstehend")
                    android.graphics.Color.rgb(0, 193, 124)
                else android.graphics.Color.rgb(200, 200, 200)
                canvas.drawRect(24f, y, 28f, y + 90f,
                    AndroidPaint().apply { color = stripeColor })

                // Number
                canvas.drawText("#${i + 1}", 36f, y + 16f,
                    AndroidPaint().apply {
                        color = android.graphics.Color.rgb(107, 122, 141)
                        textSize = 10f; isAntiAlias = true
                    })

                // Doctor name
                canvas.drawText(termin.arztName, 60f, y + 16f, titlePaint)

                // Status badge
                val statusPaint = if (termin.status == "Bevorstehend") greenPaint else grayPaint
                canvas.drawText(termin.status, pageWidth - 100f, y + 16f, statusPaint)

                // Details
                canvas.drawText("FACHBEREICH", 36f, y + 34f, labelPaint)
                canvas.drawText(termin.fachbereich, 140f, y + 34f, valuePaint)

                canvas.drawText("DATUM", 36f, y + 50f, labelPaint)
                canvas.drawText(termin.datum.take(16), 140f, y + 50f, valuePaint)

                if (termin.arztTelefon.isNotBlank()) {
                    canvas.drawText("TELEFON", 36f, y + 66f, labelPaint)
                    canvas.drawText(termin.arztTelefon, 140f, y + 66f, valuePaint)
                }

                if (termin.beschreibung.isNotBlank()) {
                    canvas.drawText("GRUND", 36f, y + 82f, labelPaint)
                    val desc = if (termin.beschreibung.length > 50)
                        termin.beschreibung.take(47) + "..." else termin.beschreibung
                    canvas.drawText(desc, 140f, y + 82f, valuePaint)
                }

                y += 100f
                canvas.drawLine(24f, y - 6f, pageWidth - 24f, y - 6f, linePaint)
            }

            // Footer
            canvas.drawText(
                "IDAS Gesundheitsportal — Vertraulich — ${
                    SimpleDateFormat("dd.MM.yyyy HH:mm", Locale.GERMANY).format(Date())}",
                24f, pageHeight - 15f, smallPaint
            )

            doc.finishPage(currentPage)

            // Save and share
            val file = File(context.cacheDir, "IDAS_Terminverlauf_$patientName.pdf")
            doc.writeTo(FileOutputStream(file))
            doc.close()

            val uri = FileProvider.getUriForFile(
                context, "${context.packageName}.fileprovider", file)
            val intent = Intent(Intent.ACTION_SEND).apply {
                type = "application/pdf"
                putExtra(Intent.EXTRA_STREAM, uri)
                putExtra(Intent.EXTRA_SUBJECT, "IDAS Terminverlauf — $patientName")
                addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
            }
            context.startActivity(Intent.createChooser(intent, "PDF teilen"))
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }
}
