package com.waveproject.kelashub

import android.app.Activity
import android.content.Intent
import android.graphics.BitmapFactory
import android.net.Uri
import android.os.Bundle
import android.provider.MediaStore
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.File
import java.io.FileOutputStream
import java.net.URL
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale
import kotlin.concurrent.thread

class PayKasActivity : AppCompatActivity() {

    private lateinit var ivQris: ImageView
    private lateinit var etAmount: EditText
    private lateinit var etDescription: EditText
    private lateinit var btnSelectProof: Button
    private lateinit var btnSubmitPay: Button
    private lateinit var ivProofPreview: ImageView
    private lateinit var btnBack: Button

    private var selectedProofUri: Uri? = null

    companion object {
        private const val PICK_PROOF_REQUEST = 2
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_pay_kas)

        ivQris = findViewById(R.id.ivQris)
        etAmount = findViewById(R.id.etAmount)
        etDescription = findViewById(R.id.etDescription)
        btnSelectProof = findViewById(R.id.btnSelectProof)
        btnSubmitPay = findViewById(R.id.btnSubmitPay)
        ivProofPreview = findViewById(R.id.ivProofPreview)
        btnBack = findViewById(R.id.btnBack)

        btnBack.setOnClickListener { finish() }

        val qrisUrl = intent.getStringExtra("QRIS_URL")
        if (!qrisUrl.isNullOrEmpty()) {
            val fullUrl = if (qrisUrl.startsWith("http") || qrisUrl.startsWith("data:image")) qrisUrl else "${BuildConfig.BASE_URL}storage/$qrisUrl"
            thread {
                try {
                    val url = URL(fullUrl)
                    val bmp = BitmapFactory.decodeStream(url.openConnection().getInputStream())
                    runOnUiThread {
                        ivQris.setImageBitmap(bmp)
                    }
                } catch (e: Exception) {
                    e.printStackTrace()
                }
            }
        } else {
            Toast.makeText(this, "Silakan lapor Bendahara untuk mengunggah QRIS kelas terlebih dahulu ya \uD83D\uDE4F", Toast.LENGTH_LONG).show()
        }

        btnSelectProof.setOnClickListener {
            val intent = Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI)
            startActivityForResult(intent, PICK_PROOF_REQUEST)
        }

        btnSubmitPay.setOnClickListener {
            btnSubmitPay.isEnabled = false
            val amountStr = etAmount.text.toString()
            val descStr = etDescription.text.toString()

            if (amountStr.isEmpty() || descStr.isEmpty() || selectedProofUri == null) {
                Toast.makeText(this, "Data belum lengkap! Harap lampirkan nominal, laporan pembayaran, dan resi foto.", Toast.LENGTH_SHORT).show()
                btnSubmitPay.isEnabled = true
                return@setOnClickListener
            }
            submitPayment(amountStr, descStr, selectedProofUri!!)
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == PICK_PROOF_REQUEST && resultCode == Activity.RESULT_OK && data != null) {
            selectedProofUri = data.data
            ivProofPreview.setImageURI(selectedProofUri)
            ivProofPreview.visibility = View.VISIBLE
            btnSubmitPay.isEnabled = true
        }
    }

    private fun submitPayment(amount: String, desc: String, uri: Uri) {
        val file = getFileFromUri(uri)
        if (file == null) {
            Toast.makeText(this, "Gagal memproses gambar mutasi", Toast.LENGTH_SHORT).show()
            return
        }

        val requestFile = file.asRequestBody("image/*".toMediaTypeOrNull())
        val bodyImage = MultipartBody.Part.createFormData("proof_image", file.name, requestFile)

        val bodyAmount = amount.toRequestBody("text/plain".toMediaTypeOrNull())
        val bodyType = "income".toRequestBody("text/plain".toMediaTypeOrNull())
        val bodyDesc = desc.toRequestBody("text/plain".toMediaTypeOrNull())
        val df = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val bodyDate = df.format(Date()).toRequestBody("text/plain".toMediaTypeOrNull())

        btnSubmitPay.isEnabled = false
        btnSubmitPay.text = "Mohon Tunggu..."

        ApiClient.apiInterface.addCashWithProof(bodyAmount, bodyType, bodyDesc, bodyDate, bodyImage).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                btnSubmitPay.isEnabled = true
                btnSubmitPay.text = "Laporkan Pembayaran"
                if (response.isSuccessful) {
                    Toast.makeText(this@PayKasActivity, "Upload sukses! Menunggu divalidasi dan disetujui Bendahara ya \uD83D\uDE01", Toast.LENGTH_LONG).show()
                    finish()
                } else {
                    Toast.makeText(this@PayKasActivity, "Gagal menyimpan laporan kas.", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                btnSubmitPay.isEnabled = true
                btnSubmitPay.text = "Laporkan Pembayaran"
                Toast.makeText(this@PayKasActivity, "Timeout koneksi, periksa internet Anda.", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun getFileFromUri(uri: Uri): File? {
        return try {
            val inputStream = contentResolver.openInputStream(uri)
            val file = File(cacheDir, "bukti_kas_${System.currentTimeMillis()}.jpg")
            val outputStream = FileOutputStream(file)
            inputStream?.copyTo(outputStream)
            inputStream?.close()
            outputStream.close()
            file
        } catch (e: Exception) {
            e.printStackTrace()
            null
        }
    }
}
