package com.idas.app.models

import android.os.Parcel
import android.os.Parcelable

data class Symptom(val id: Int, val name: String)

data class Arzt(
    val arztId: Int, val name: String, val telefon: String,
    val email: String, val addresse: String
)

data class FachbereichResult(
    val fachbereichId: Int, val fachbereich: String,
    val punkte: Int, val aerzte: List<Arzt>
)

data class Termin(
    val terminId: Int, val datum: String, val beschreibung: String,
    val arztName: String, val arztTelefon: String, val arztEmail: String,
    val fachbereich: String, val status: String,
    val symptome: List<String> = emptyList()  // NEW
)

data class Profil(
    val patientId: Int, val vorname: String, val nachname: String,
    val email: String, val telefon: String, val wohnort: String,
    val plz: String, val adresse: String, val geburtsdatum: String,
    val geschlecht: String
)

data class BookingConfirmData(
    val terminId: Int, val arztName: String, val fachbereich: String,
    val datum: String, val patientName: String, val beschreibung: String
) : Parcelable {
    constructor(parcel: Parcel) : this(
        parcel.readInt(), parcel.readString() ?: "", parcel.readString() ?: "",
        parcel.readString() ?: "", parcel.readString() ?: "", parcel.readString() ?: ""
    )
    override fun writeToParcel(parcel: Parcel, flags: Int) {
        parcel.writeInt(terminId); parcel.writeString(arztName)
        parcel.writeString(fachbereich); parcel.writeString(datum)
        parcel.writeString(patientName); parcel.writeString(beschreibung)
    }
    override fun describeContents() = 0
    companion object CREATOR : Parcelable.Creator<BookingConfirmData> {
        override fun createFromParcel(parcel: Parcel) = BookingConfirmData(parcel)
        override fun newArray(size: Int) = arrayOfNulls<BookingConfirmData>(size)
    }
}
