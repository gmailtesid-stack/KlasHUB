package com.waveproject.kelashub

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class ValidationAdapter(
    private var list: List<PendingValidationItem>,
    private val onApprove: (PendingValidationItem) -> Unit
) : RecyclerView.Adapter<ValidationAdapter.VH>() {

    class VH(v: View) : RecyclerView.ViewHolder(v) {
        val tvValType: TextView = v.findViewById(R.id.tvValType)
        val tvValTitle: TextView = v.findViewById(R.id.tvValTitle)
        val tvValDesc: TextView = v.findViewById(R.id.tvValDesc)
        val ivProofPreview: android.widget.ImageView = v.findViewById(R.id.ivProofPreview)
        val btnApprove: Button = v.findViewById(R.id.btnApprove)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): VH {
        return VH(LayoutInflater.from(parent.context).inflate(R.layout.item_validation, parent, false))
    }

    override fun onBindViewHolder(holder: VH, position: Int) {
        val item = list[position]
        holder.tvValType.text = item.type.uppercase()
        holder.tvValTitle.text = item.title
        holder.tvValDesc.text = item.description

        if (item.proofImage != null) {
            holder.ivProofPreview.visibility = View.VISIBLE
            val urlStr = "https://klas-hub.vercel.app/storage/" + item.proofImage
            Thread {
                try {
                    val url = java.net.URL(urlStr)
                    val bmp = android.graphics.BitmapFactory.decodeStream(url.openConnection().getInputStream())
                    holder.ivProofPreview.post {
                        holder.ivProofPreview.setImageBitmap(bmp)
                    }
                } catch (e: Exception) {
                    e.printStackTrace()
                }
            }.start()
        } else {
            holder.ivProofPreview.visibility = View.GONE
            holder.ivProofPreview.setImageBitmap(null)
        }

        holder.btnApprove.setOnClickListener {
            onApprove(item)
        }
    }

    override fun getItemCount() = list.size

    fun updateData(newData: List<PendingValidationItem>) {
        list = newData
        notifyDataSetChanged()
    }
}
