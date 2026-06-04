import React from 'react';
import {
  ResponsiveContainer,
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  PieChart,
  Pie,
  Cell,
  BarChart,
  Bar,
  LabelList,
} from 'recharts';

const chartColors = {
  emerald: ['#047857', '#10b981', '#6ee7b7', '#a7f3d0', '#d9f99d'],
  neutral: ['#64748b', '#cbd5e1', '#e2e8f0', '#f8fafc'],
};

const formatCurrency = (value) => {
  if (value == null) {
    return '-';
  }

  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(value);
};

const tooltipFormatter = (value) => [formatCurrency(value), 'Nominal'];

export default function FinanceDashboardCharts({ monthlyRevenue = [], vendorExpenses = [], paymentStatus = [] }) {
  const lineData = monthlyRevenue.map((item) => ({
    ...item,
    revenue: Number(item.revenue ?? 0),
  }));

  const pieData = vendorExpenses.map((item) => ({
    ...item,
    cost: Number(item.cost ?? 0),
  }));

  const barData = paymentStatus.map((item) => ({
    ...item,
    nominal: Number(item.nominal ?? 0),
  }));

  return (
    <section className="grid grid-cols-1 gap-6 mb-8 xl:grid-cols-[2fr_1fr]">
      <article className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <p className="text-sm font-semibold text-gray-500">Tren Pendapatan Bulanan</p>
            <h2 className="text-2xl font-bold text-gray-900 mt-1">Ringkasan Pendapatan</h2>
            <p className="text-xs text-gray-500 mt-1">12 bulan terakhir, ditampilkan per bulan.</p>
          </div>
          <div className="rounded-3xl bg-emerald-50 px-4 py-3 text-emerald-900 text-sm font-semibold">
            Total terakhir: {formatCurrency(lineData.reduce((sum, row) => sum + row.revenue, 0))}
          </div>
        </div>

        <div className="mt-6 h-[320px]">
          <ResponsiveContainer width="100%" height="100%">
            <LineChart data={lineData} margin={{ top: 20, right: 20, left: 0, bottom: 0 }}>
              <CartesianGrid stroke="#E5E7EB" strokeDasharray="3 3" />
              <XAxis dataKey="month" stroke="#64748B" tickLine={false} axisLine={false} />
              <YAxis stroke="#64748B" tickLine={false} axisLine={false} tickFormatter={(value) => `Rp${value / 1000}k`} />
              <Tooltip formatter={tooltipFormatter} contentStyle={{ borderRadius: 16, border: '1px solid #E5E7EB' }} />
              <Legend verticalAlign="top" align="right" iconType="circle" />
              <Line type="monotone" dataKey="revenue" name="Pendapatan" stroke={chartColors.emerald[0]} strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </article>

      <div className="grid grid-cols-1 gap-6">
        <article className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 h-full">
          <div>
            <p className="text-sm font-semibold text-gray-500">Proporsi Biaya Operasional</p>
            <h2 className="text-lg font-bold text-gray-900 mt-1">Per Kategori Vendor</h2>
            <p className="text-xs text-gray-500 mt-1">Menunjukkan dominasi biaya berdasarkan kategori vendor.</p>
          </div>
          <div className="mt-6 h-[280px]">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={pieData}
                  dataKey="cost"
                  nameKey="category"
                  innerRadius={72}
                  outerRadius={108}
                  paddingAngle={4}
                  stroke="none"
                >
                  {pieData.map((entry, index) => (
                    <Cell key={`cell-${entry.category}`} fill={chartColors.emerald[index % chartColors.emerald.length]} />
                  ))}
                </Pie>
                <Tooltip formatter={tooltipFormatter} contentStyle={{ borderRadius: 16, border: '1px solid #E5E7EB' }} />
                <Legend layout="vertical" verticalAlign="middle" align="right" iconType="circle" />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </article>

        <article className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 h-full">
          <div>
            <p className="text-sm font-semibold text-gray-500">Status Pembayaran Klien</p>
            <h2 className="text-lg font-bold text-gray-900 mt-1">Tagihan vs Pelunasan</h2>
            <p className="text-xs text-gray-500 mt-1">Total Tagihan, sudah dibayar, dan sisa pelunasan.</p>
          </div>
          <div className="mt-6 h-[280px]">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={barData} margin={{ top: 10, right: 12, left: 0, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#E5E7EB" />
                <XAxis dataKey="label" stroke="#64748B" tickLine={false} axisLine={false} />
                <YAxis stroke="#64748B" tickFormatter={(value) => `Rp${value / 1000}k`} tickLine={false} axisLine={false} />
                <Tooltip formatter={tooltipFormatter} contentStyle={{ borderRadius: 16, border: '1px solid #E5E7EB' }} />
                <Bar dataKey="nominal" fill={chartColors.emerald[1]} radius={[12, 12, 0, 0]}>
                  <LabelList dataKey="nominal" position="top" formatter={formatCurrency} />
                </Bar>
              </BarChart>
            </ResponsiveContainer>
          </div>
        </article>
      </div>
    </section>
  );
}
