package com.waveproject.kelashub

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class ModuleAdapter(
    private var modules: List<Module>,
    private val onLongClick: ((Module, View) -> Unit)? = null
) : RecyclerView.Adapter<ModuleAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvSubject: TextView = view.findViewById(R.id.tvSubject)
        val tvTitle: TextView = view.findViewById(R.id.tvTitle)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_module, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val module = modules[position]
        holder.tvSubject.text = module.subject_name
        holder.tvTitle.text = module.title

        holder.itemView.setOnLongClickListener {
            onLongClick?.invoke(module, it)
            true
        }
    }

    override fun getItemCount() = modules.size

    fun updateData(newModules: List<Module>) {
        modules = newModules
        notifyDataSetChanged()
    }
}
