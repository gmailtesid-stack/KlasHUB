package com.waveproject.kelashub

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.provider.OpenableColumns
import android.view.View
import android.widget.Button
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.RadioButton
import android.widget.RadioGroup
import android.widget.TextView
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

class InputModuleActivity : AppCompatActivity() {

    private var selectedFileUri: Uri? = null
    private lateinit var tvFileName: TextView

    companion object {
        const val PICK_FILE_REQUEST_CODE = 404
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_input_module)

        findViewById<Button>(R.id.btnBack).setOnClickListener { finish() }

        val etSubjectName = findViewById<EditText>(R.id.etSubjectName)
        val rgType = findViewById<RadioGroup>(R.id.rgType)
        val rbFile = findViewById<RadioButton>(R.id.rbFile)
        val containerLink = findViewById<LinearLayout>(R.id.containerLink)
        val containerFile = findViewById<LinearLayout>(R.id.containerFile)
        val etTitle = findViewById<EditText>(R.id.etTitle)
        val etLinkUrl = findViewById<EditText>(R.id.etLinkUrl)
        val btnPickFile = findViewById<Button>(R.id.btnPickFile)
        tvFileName = findViewById<TextView>(R.id.tvFileName)
        val btnSubmit = findViewById<Button>(R.id.btnSubmit)

        rgType.setOnCheckedChangeListener { _, checkedId ->
            if (checkedId == R.id.rbFile) {
                containerFile.visibility = View.VISIBLE
                containerLink.visibility = View.GONE
            } else {
                containerFile.visibility = View.GONE
                containerLink.visibility = View.VISIBLE
            }
        }

        btnPickFile.setOnClickListener {
            val intent = Intent(Intent.ACTION_GET_CONTENT).apply { type = "*/*" }
            startActivityForResult(intent, PICK_FILE_REQUEST_CODE)
        }

        btnSubmit.setOnClickListener {
            val subjectName = etSubjectName.text.toString().trim()
            val type = if (rbFile.isChecked) "file" else "link"

            if (subjectName.isEmpty()) {
                Toast.makeText(this, "Mohon isi Nama Mata Kuliah", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if (type == "file" && selectedFileUri == null) {
                Toast.makeText(this, "Mohon pilih file untuk diupload", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            } else if (type == "link" && (etTitle.text.toString().isEmpty() || etLinkUrl.text.toString().isEmpty())) {
                Toast.makeText(this, "Mohon isi Judul dan URL Link", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            btnSubmit.isEnabled = false
            btnSubmit.text = "Mengupload..."

            val reqSubject = subjectName.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqType = type.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqTitle = etTitle.text.toString().trim().ifEmpty { null }?.toRequestBody("text/plain".toMediaTypeOrNull())
            val reqLink = etLinkUrl.text.toString().trim().ifEmpty { null }?.toRequestBody("text/plain".toMediaTypeOrNull())

            var filePart: MultipartBody.Part? = null

            if (type == "File" && selectedFileUri != null) {
                val tempFile = getFileFromUri(selectedFileUri!!)
                if (tempFile != null) {
                    val reqFile = tempFile.asRequestBody("application/octet-stream".toMediaTypeOrNull())
                    filePart = MultipartBody.Part.createFormData("file", tempFile.name, reqFile)
                }
            }

            ApiClient.apiInterface.storeModule(reqSubject, reqType, reqTitle, reqLink, filePart)
                .enqueue(object : Callback<Void> {
                    override fun onResponse(call: Call<Void>, response: Response<Void>) {
                        if (response.isSuccessful) {
                            Toast.makeText(this@InputModuleActivity, "Modul berhasil ditambahkan!", Toast.LENGTH_SHORT).show()
                            finish()
                        } else {
                            Toast.makeText(this@InputModuleActivity, "Gagal menambahkan modul", Toast.LENGTH_SHORT).show()
                            btnSubmit.isEnabled = true
                            btnSubmit.text = "Simpan Modul"
                        }
                    }

                    override fun onFailure(call: Call<Void>, t: Throwable) {
                        Toast.makeText(this@InputModuleActivity, "Kesalahan jaringan", Toast.LENGTH_SHORT).show()
                        btnSubmit.isEnabled = true
                        btnSubmit.text = "Simpan Modul"
                    }
                })
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == PICK_FILE_REQUEST_CODE && resultCode == RESULT_OK) {
            data?.data?.let { uri ->
                selectedFileUri = uri
                tvFileName.text = getFileName(uri) ?: "Unknown File"
            }
        }
    }

    private fun getFileFromUri(uri: Uri): File? {
        val fileName = getFileName(uri) ?: "module_temp"
        val tempFile = File(cacheDir, fileName)
        try {
            contentResolver.openInputStream(uri)?.use { inputStream ->
                FileOutputStream(tempFile).use { outputStream ->
                    inputStream.copyTo(outputStream)
                }
            }
            return tempFile
        } catch (e: Exception) {
            e.printStackTrace()
        }
        return null
    }

    private fun getFileName(uri: Uri): String? {
        var result: String? = null
        if (uri.scheme == "content") {
            contentResolver.query(uri, null, null, null, null)?.use { cursor ->
                if (cursor.moveToFirst()) {
                    val idx = cursor.getColumnIndex(OpenableColumns.DISPLAY_NAME)
                    if (idx != -1) {
                        result = cursor.getString(idx)
                    }
                }
            }
        }
        if (result == null) {
            result = uri.path
            val cut = result?.lastIndexOf('/') ?: -1
            if (cut != -1) {
                result = result?.substring(cut + 1)
            }
        }
        return result
    }
}
