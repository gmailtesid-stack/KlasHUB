package com.waveproject.kelashub

import android.content.Context
import android.content.Intent
import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewmodel.compose.viewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.NumberFormat
import java.util.Locale

// --- UI STATES ---
sealed class DashboardUiState {
    object Loading : DashboardUiState()
    data class Success(val data: DashboardDataResponse, val isOffline: Boolean = false) : DashboardUiState()
    data class Error(val message: String) : DashboardUiState()
}

// --- VIEWMODEL ---
class DashboardViewModel : ViewModel() {
    private val _uiState = MutableStateFlow<DashboardUiState>(DashboardUiState.Loading)
    val uiState: StateFlow<DashboardUiState> = _uiState.asStateFlow()

    private var selectedSemester: Int? = null

    init {
        fetchData(null)
    }

    fun setSemester(semester: Int) {
        selectedSemester = semester
        fetchData(null)
    }

    fun fetchData(context: Context? = null) {
        _uiState.value = DashboardUiState.Loading
        
        ApiClient.apiInterface.getDashboardData(selectedSemester).enqueue(object : Callback<DashboardDataResponse> {
            override fun onResponse(call: Call<DashboardDataResponse>, response: Response<DashboardDataResponse>) {
                if (response.isSuccessful) {
                    val data = response.body()
                    if (data != null) {
                        context?.let { ctx ->
                            try {
                                val prefs = ctx.getSharedPreferences("OfflineCache", Context.MODE_PRIVATE)
                                val json = com.google.gson.Gson().toJson(data)
                                val cacheKey = if (selectedSemester != null) "dashboard_data_$selectedSemester" else "dashboard_data_active"
                                prefs.edit().putString(cacheKey, json).apply()
                            } catch (e: Exception) {}
                        }
                        _uiState.value = DashboardUiState.Success(data)
                    } else {
                        _uiState.value = DashboardUiState.Error("Data kosong")
                    }
                } else {
                    if (response.code() == 401 && context != null) {
                        context.startActivity(Intent(context, LoginActivity::class.java))
                    } else {
                        _uiState.value = DashboardUiState.Error("Gagal memuat data (Code: ${response.code()})")
                    }
                }
            }

            override fun onFailure(call: Call<DashboardDataResponse>, t: Throwable) {
                if (context != null) {
                    try {
                        val prefs = context.getSharedPreferences("OfflineCache", Context.MODE_PRIVATE)
                        val cacheKey = if (selectedSemester != null) "dashboard_data_$selectedSemester" else "dashboard_data_active"
                        val json = prefs.getString(cacheKey, null)
                        if (json != null) {
                            val data = com.google.gson.Gson().fromJson(json, DashboardDataResponse::class.java)
                            _uiState.value = DashboardUiState.Success(data, isOffline = true)
                            return
                        }
                    } catch (e: Exception) {
                        // ignore error
                    }
                }
                _uiState.value = DashboardUiState.Error("Error koneksi, cache kosong")
            }
        })
    }
}

// --- COLORS ---
object StealthColors {
    val Background = Color(0xFF0D0F14)
    val Surface = Color(0xFF1A1D26)
    val Primary = Color(0xFF2196F3)
    val TextPrimary = Color(0xFFFFFFFF)
    val TextSecondary = Color(0xFF8A8D98)
    val Income = Color(0xFF10B981)
    val Expense = Color(0xFFEF4444)
}

// --- COMPOSABLE SCREEN ---
@Composable
fun DashboardScreen(viewModel: DashboardViewModel = viewModel()) {
    val uiState by viewModel.uiState.collectAsState()
    val context = LocalContext.current

    LaunchedEffect(Unit) {
        viewModel.fetchData(context)
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(StealthColors.Background)
    ) {
        when (val state = uiState) {
            is DashboardUiState.Loading -> {
                CircularProgressIndicator(
                    modifier = Modifier.align(Alignment.Center),
                    color = StealthColors.Primary
                )
            }
            is DashboardUiState.Error -> {
                ErrorState(
                    message = state.message,
                    onRetry = { viewModel.fetchData(context) },
                    modifier = Modifier.align(Alignment.Center)
                )
            }
            is DashboardUiState.Success -> {
                if (state.isOffline) {
                    LaunchedEffect(state.isOffline) {
                        Toast.makeText(context, "Mode Offline Aktif (\u26A0\uFE0F)", Toast.LENGTH_LONG).show()
                    }
                }
                DashboardContent(
                    data = state.data,
                    onSemesterSelect = { viewModel.setSemester(it) },
                    onPayKas = { qrisUrl ->
                        val intent = Intent(context, PayKasActivity::class.java)
                        intent.putExtra("QRIS_URL", qrisUrl)
                        context.startActivity(intent)
                    }
                )
            }
        }
    }
}

@Composable
fun ErrorState(message: String, onRetry: () -> Unit, modifier: Modifier = Modifier) {
    Column(
        modifier = modifier.padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Text(
            text = message,
            color = StealthColors.TextSecondary,
            fontSize = 14.sp
        )
        Spacer(modifier = Modifier.height(16.dp))
        Button(
            onClick = onRetry,
            colors = ButtonDefaults.buttonColors(containerColor = StealthColors.Primary),
            shape = RoundedCornerShape(16.dp)
        ) {
            Text(text = "Coba Lagi", color = StealthColors.TextPrimary)
        }
    }
}

@Composable
fun DashboardContent(
    data: DashboardDataResponse,
    onSemesterSelect: (Int) -> Unit,
    onPayKas: (String?) -> Unit
) {
    LazyColumn(
        modifier = Modifier.fillMaxSize(),
        contentPadding = PaddingValues(bottom = 24.dp)
    ) {
        // App Header
        item {
            HeaderSection(classSemester = data.classSemester, onSemesterSelect = onSemesterSelect)
        }

        // Saldo Kas Kelas Main Card
        item {
            SaldoCard(data = data, onPayKas = { onPayKas(data.qrisImage) })
        }

        // Tugas & Deadline Section
        item {
            SectionTitle(title = "TUGAS & DEADLINE")
        }
        items(data.assignments) { assignment ->
            AssignmentItem(assignment)
        }

        // Modules Section
        item {
            SectionTitle(title = "MODUL MATERI")
            LazyRow(
                horizontalArrangement = Arrangement.spacedBy(16.dp),
                contentPadding = PaddingValues(horizontal = 24.dp)
            ) {
                items(data.modules) { module ->
                    ModuleItem(module)
                }
            }
            Spacer(modifier = Modifier.height(16.dp))
        }
    }
}

@Composable
fun HeaderSection(classSemester: Int, onSemesterSelect: (Int) -> Unit) {
    var expanded by remember { mutableStateOf(false) }

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 24.dp, vertical = 24.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = "KelasHUB",
            fontSize = 24.sp,
            fontWeight = FontWeight.Bold,
            color = StealthColors.TextPrimary
        )

        Box {
            TextButton(onClick = { expanded = true }) {
                Text(
                    text = "Semester $classSemester ▼",
                    color = StealthColors.Primary,
                    fontSize = 14.sp
                )
            }
            DropdownMenu(
                expanded = expanded,
                onDismissRequest = { expanded = false },
                modifier = Modifier.background(StealthColors.Surface)
            ) {
                for (i in 1..classSemester) {
                    DropdownMenuItem(
                        text = { Text("Arsip Semester $i", color = StealthColors.TextPrimary) },
                        onClick = {
                            expanded = false
                            onSemesterSelect(i)
                        }
                    )
                }
            }
        }
    }
}

@Composable
fun SaldoCard(data: DashboardDataResponse, onPayKas: () -> Unit) {
    val income = data.cashTransactions.filter { it.type == "income" }.sumOf { it.amount }
    val expense = data.cashTransactions.filter { it.type == "expense" }.sumOf { it.amount }
    val saldo = income - expense

    val formatter = NumberFormat.getCurrencyInstance(Locale("id", "ID"))

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 24.dp)
            .padding(bottom = 24.dp),
        shape = RoundedCornerShape(24.dp),
        colors = CardDefaults.cardColors(containerColor = StealthColors.Surface)
    ) {
        Column(
            modifier = Modifier.padding(24.dp)
        ) {
            Text(
                text = "SALDO KAS KELAS",
                fontSize = 13.sp,
                fontWeight = FontWeight.Bold,
                color = StealthColors.TextSecondary
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = formatter.format(saldo),
                fontSize = 32.sp,
                fontWeight = FontWeight.Bold,
                color = StealthColors.TextPrimary
            )
            Spacer(modifier = Modifier.height(24.dp))
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Column {
                    Text(text = "Pemasukan", fontSize = 12.sp, color = StealthColors.TextSecondary)
                    Text(text = formatter.format(income), fontSize = 16.sp, fontWeight = FontWeight.SemiBold, color = StealthColors.Income)
                }
                Column(horizontalAlignment = Alignment.End) {
                    Text(text = "Pengeluaran", fontSize = 12.sp, color = StealthColors.TextSecondary)
                    Text(text = formatter.format(expense), fontSize = 16.sp, fontWeight = FontWeight.SemiBold, color = StealthColors.Expense)
                }
            }
            Spacer(modifier = Modifier.height(24.dp))
            Button(
                onClick = onPayKas,
                modifier = Modifier.fillMaxWidth(),
                colors = ButtonDefaults.buttonColors(containerColor = StealthColors.Primary),
                shape = RoundedCornerShape(16.dp)
            ) {
                Text(
                    text = "Bayar Kas Kelas",
                    color = StealthColors.TextPrimary,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(vertical = 8.dp)
                )
            }
        }
    }
}

@Composable
fun SectionTitle(title: String) {
    Text(
        text = title,
        fontSize = 13.sp,
        fontWeight = FontWeight.Bold,
        color = StealthColors.TextSecondary,
        modifier = Modifier.padding(horizontal = 24.dp, vertical = 12.dp)
    )
}

@Composable
fun AssignmentItem(assignment: Assignment) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 24.dp, vertical = 6.dp)
            .clickable { /* Handle click */ },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = StealthColors.Surface)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = assignment.title ?: "",
                    fontSize = 16.sp,
                    fontWeight = FontWeight.SemiBold,
                    color = StealthColors.TextPrimary
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = assignment.deadline ?: "Tidak ada deadline",
                    fontSize = 12.sp,
                    color = StealthColors.TextSecondary
                )
            }
        }
    }
}

@Composable
fun ModuleItem(module: Module) {
    Card(
        modifier = Modifier
            .width(160.dp)
            .clickable { /* Handle click */ },
        shape = RoundedCornerShape(16.dp),
        colors = CardDefaults.cardColors(containerColor = StealthColors.Surface)
    ) {
        Column(
            modifier = Modifier.padding(16.dp)
        ) {
            Text(
                text = module.title ?: "",
                fontSize = 16.sp,
                fontWeight = FontWeight.SemiBold,
                color = StealthColors.TextPrimary,
                maxLines = 2
            )
            Spacer(modifier = Modifier.height(8.dp))
            Text(
                text = "Modul",
                fontSize = 12.sp,
                color = StealthColors.TextSecondary
            )
        }
    }
}
