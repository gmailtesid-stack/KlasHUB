package com.waveproject.kelashub

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class ManageStudentsAdapter(
    private var list: List<Student>,
    private val onOptionsClick: (Student, View) -> Unit
) : RecyclerView.Adapter<ManageStudentsAdapter.VH>() {

    class VH(v: View) : RecyclerView.ViewHolder(v) {
        val tvStudentName: TextView = v.findViewById(R.id.tvStudentName)
        val tvStudentNim: TextView = v.findViewById(R.id.tvStudentNim)
        val tvStudentRole: TextView = v.findViewById(R.id.tvStudentRole)
        val btnOptions: ImageView = v.findViewById(R.id.btnOptions)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): VH {
        return VH(LayoutInflater.from(parent.context).inflate(R.layout.item_manage_student, parent, false))
    }

    override fun onBindViewHolder(holder: VH, position: Int) {
        val item = list[position]
        holder.tvStudentName.text = item.name
        holder.tvStudentNim.text = item.nim
        holder.tvStudentRole.text = item.role.uppercase().replace("_", " ")

        holder.btnOptions.setOnClickListener {
            onOptionsClick(item, it)
        }
    }

    override fun getItemCount() = list.size

    fun updateData(newData: List<Student>) {
        list = newData
        notifyDataSetChanged()
    }
}
