package com.waveproject.kelashub

import android.app.Activity
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.provider.MediaStore
import android.widget.Button
import android.widget.ImageView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.io.File
import java.io.FileOutputStream

class UploadQrisActivity : AppCompatActivity() {

    private lateinit var ivQrisPreview: ImageView
    private lateinit var btnSelectImage: Button
    private lateinit var btnUpload: Button
    private lateinit var btnBack: Button

    private var selectedImageUri: Uri? = null

    companion object {
        private const val PICK_IMAGE_REQUEST = 1
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_upload_qris)

        ivQrisPreview = findViewById(R.id.ivQrisPreview)
        btnSelectImage = findViewById(R.id.btnSelectImage)
        btnUpload = findViewById(R.id.btnUpload)
        btnBack = findViewById(R.id.btnBack)

        btnBack.setOnClickListener { finish() }

        btnSelectImage.setOnClickListener {
            val intent = Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI)
            startActivityForResult(intent, PICK_IMAGE_REQUEST)
        }

        btnUpload.setOnClickListener {
            selectedImageUri?.let { uri -> uploadImage(uri) }
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == PICK_IMAGE_REQUEST && resultCode == Activity.RESULT_OK && data != null) {
            selectedImageUri = data.data
            ivQrisPreview.setImageURI(selectedImageUri)
            btnUpload.isEnabled = true
        }
    }

    private fun uploadImage(uri: Uri) {
        val file = getFileFromUri(uri)
        if (file == null) {
            Toast.makeText(this, "Gagal memproses gambar", Toast.LENGTH_SHORT).show()
            return
        }

        val requestFile = file.asRequestBody("image/*".toMediaTypeOrNull())
        val body = MultipartBody.Part.createFormData("qris_image", file.name, requestFile)

        btnUpload.isEnabled = false
        btnUpload.text = "Mengunggah..."

        ApiClient.apiInterface.uploadQris(body).enqueue(object : Callback<ApiResponse> {
            override fun onResponse(call: Call<ApiResponse>, response: Response<ApiResponse>) {
                btnUpload.isEnabled = true
                btnUpload.text = "Simpan dan Unggah QRIS"
                if (response.isSuccessful && response.body()?.success == true) {
                    Toast.makeText(this@UploadQrisActivity, "QRIS Berhasil Diperbarui!", Toast.LENGTH_LONG).show()
                    finish()
                } else {
                    Toast.makeText(this@UploadQrisActivity, "Gagal: ${response.body()?.message ?: "Unknown error"}", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ApiResponse>, t: Throwable) {
                btnUpload.isEnabled = true
                btnUpload.text = "Simpan dan Unggah QRIS"
                Toast.makeText(this@UploadQrisActivity, "Koneksi Gagal/Timeout", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun getFileFromUri(uri: Uri): File? {
        return try {
            val inputStream = contentResolver.openInputStream(uri)
            val file = File(cacheDir, "upload_qris_${System.currentTimeMillis()}.jpg")
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
