import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
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

/** High-contrast greens only — no pale/washed-out slices */
const VENDOR_GREEN_PALETTE = [
  '#022c22',
  '#064e3b',
  '#065f46',
  '#047857',
  '#059669',
  '#0f766e',
  '#0d9488',
  '#15803d',
  '#166534',
  '#14532d',
];

const CATEGORY_COLOR_MAP = {
  Dekorasi: '#047857',
  Entertainment: '#064e3b',
  Catering: '#059669',
  Makeup: '#0f766e',
  Dokumentasi: '#065f46',
  'Foto & Video': '#0d9488',
  MC: '#15803d',
  Venue: '#022c22',
  Busana: '#166534',
  Lainnya: '#14532d',
};

const SLICE_BORDER = { stroke: '#ffffff', strokeWidth: 3 };

const chartColors = {
  emerald: VENDOR_GREEN_PALETTE,
  neutral: ['#475569', '#94a3b8', '#e2e8f0', '#f8fafc'],
};

function getVendorCategoryColor(category, index) {
  const key = String(category ?? '').trim();
  if (CATEGORY_COLOR_MAP[key]) {
    return CATEGORY_COLOR_MAP[key];
  }
  return VENDOR_GREEN_PALETTE[index % VENDOR_GREEN_PALETTE.length];
}

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

function useStableChartSize(containerRef, minHeight = 280) {
  const lastGood = useRef({ width: 320, height: minHeight });
  const [size, setSize] = useState(() => ({ ...lastGood.current }));

  const measure = useCallback(() => {
    const el = containerRef.current;
    if (!el) {
      return;
    }

    const rect = el.getBoundingClientRect();
    const width = Math.floor(rect.width);
    const height = Math.floor(rect.height);

    if (width >= 120 && height >= 120) {
      lastGood.current = { width, height };
      setSize({ width, height });
      return;
    }

    if (lastGood.current.width >= 120) {
      setSize({ ...lastGood.current });
    }
  }, [containerRef]);

  useEffect(() => {
    const el = containerRef.current;
    if (!el) {
      return undefined;
    }

    measure();

    const resizeObserver = new ResizeObserver(measure);
    resizeObserver.observe(el);

    const intersectionObserver = new IntersectionObserver(
      (entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
          requestAnimationFrame(measure);
        }
      },
      { threshold: 0.05 }
    );
    intersectionObserver.observe(el);

    window.addEventListener('resize', measure);

    return () => {
      resizeObserver.disconnect();
      intersectionObserver.disconnect();
      window.removeEventListener('resize', measure);
    };
  }, [containerRef, measure]);

  return size;
}

function ChartLoadingFallback({ label = 'Memuat grafik...' }) {
  return (
    <div
      className="flex flex-col items-center justify-center gap-3 w-full h-full min-h-[240px]"
      role="status"
      aria-live="polite"
      aria-busy="true"
    >
      <div className="w-10 h-10 border-4 border-emerald-200 border-t-emerald-800 rounded-full animate-spin" />
      <p className="text-sm text-gray-700 font-medium">{label}</p>
    </div>
  );
}

function DonutLegend({ data }) {
  if (!data.length) {
    return null;
  }

  return (
    <ul className="flex flex-col gap-2.5 shrink-0 md:max-w-[148px] md:pl-2">
      {data.map((item, index) => {
        const color = getVendorCategoryColor(item.category, index);
        return (
          <li key={`${item.category}-${index}`} className="flex items-start gap-2.5">
            <span
              className="w-3.5 h-3.5 rounded-full shrink-0 mt-0.5 ring-2 ring-white shadow-sm"
              style={{ backgroundColor: color }}
              aria-hidden="true"
            />
            <span className="text-[13px] font-semibold text-gray-800 leading-snug">{item.category}</span>
          </li>
        );
      })}
    </ul>
  );
}

function OperationalCostDonutChart({ data }) {
  const containerRef = useRef(null);
  const { width, height } = useStableChartSize(containerRef, 280);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const chartReady = mounted && width >= 120 && height >= 120;

  if (!data.length) {
    return (
      <div className="flex flex-col items-center justify-center text-center min-h-[240px]">
        <p className="text-sm text-gray-700 font-medium">Tidak ada data biaya tersedia.</p>
        <p className="text-xs text-gray-500 mt-1">Data akan ditampilkan saat tersedia.</p>
      </div>
    );
  }

  return (
    <div className="flex flex-col md:flex-row md:items-center gap-5 w-full min-h-[280px]">
      <div
        ref={containerRef}
        className="flex-1 w-full min-h-[240px] h-[260px] md:h-[280px] relative overflow-visible"
        style={{ contain: 'layout style' }}
      >
        {!chartReady ? (
          <ChartLoadingFallback label="Memuat proporsi biaya..." />
        ) : (
          <PieChart width={width} height={height} style={{ overflow: 'visible' }}>
            <Pie
              data={data}
              dataKey="cost"
              nameKey="category"
              cx="50%"
              cy="50%"
              innerRadius="58%"
              outerRadius="82%"
              paddingAngle={4}
              stroke={SLICE_BORDER.stroke}
              strokeWidth={SLICE_BORDER.strokeWidth}
              isAnimationActive={false}
            >
              {data.map((entry, index) => {
                const cat = String(entry.category ?? `category-${index}`);
                const color = getVendorCategoryColor(entry.category, index);
                return (
                  <Cell
                    key={`cell-${cat}-${index}`}
                    fill={color}
                    stroke={SLICE_BORDER.stroke}
                    strokeWidth={SLICE_BORDER.strokeWidth}
                  />
                );
              })}
            </Pie>
            <Tooltip
              formatter={tooltipFormatter}
              contentStyle={{
                borderRadius: 16,
                border: '1px solid #E5E7EB',
                color: '#111827',
                zIndex: 9999,
              }}
              wrapperStyle={{ zIndex: 9999 }}
              itemStyle={{ color: '#111827' }}
              labelStyle={{ color: '#374151', fontWeight: 600 }}
            />
          </PieChart>
        )}
      </div>
      <DonutLegend data={data} />
    </div>
  );
}

export default function FinanceDashboardCharts({ monthlyRevenue = [], vendorExpenses = [], paymentStatus = [] }) {
  const lineData = useMemo(
    () =>
      monthlyRevenue.map((item) => ({
        ...item,
        revenue: Number(item.revenue ?? 0),
      })),
    [monthlyRevenue]
  );

  const pieData = useMemo(
    () =>
      vendorExpenses
        .map((item) => ({ ...item, cost: Number(item.cost ?? 0) }))
        .filter((item) => item.cost > 0),
    [vendorExpenses]
  );

  const barData = useMemo(
    () => paymentStatus.map((item) => ({ ...item, nominal: Number(item.nominal ?? 0) })),
    [paymentStatus]
  );

  return (
    <section className="grid grid-cols-1 gap-6 mb-8 xl:grid-cols-[2fr_1fr]">
      <article className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 overflow-hidden">
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

        <div className="mt-6 min-h-[300px] h-[320px]">
          <ResponsiveContainer width="100%" height="100%" debounce={80}>
            <LineChart data={lineData} margin={{ top: 20, right: 20, left: 0, bottom: 0 }}>
              <CartesianGrid stroke="#E5E7EB" strokeDasharray="3 3" />
              <XAxis dataKey="month" stroke="#475569" tickLine={false} axisLine={false} />
              <YAxis stroke="#475569" tickLine={false} axisLine={false} tickFormatter={(value) => `Rp${value / 1000}k`} />
              <Tooltip formatter={tooltipFormatter} contentStyle={{ borderRadius: 16, border: '1px solid #E5E7EB' }} />
              <Legend verticalAlign="top" align="right" iconType="circle" />
              <Line type="monotone" dataKey="revenue" name="Pendapatan" stroke={chartColors.emerald[3]} strokeWidth={3} dot={{ r: 4 }} activeDot={{ r: 6 }} />
            </LineChart>
          </ResponsiveContainer>
        </div>
      </article>

      <div className="grid grid-cols-1 gap-6">
        <article className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 h-full overflow-visible">
          <div>
            <p className="text-sm font-semibold text-gray-500">Proporsi Biaya Operasional</p>
            <h2 className="text-lg font-bold text-gray-900 mt-1">Per Kategori Vendor</h2>
            <p className="text-xs text-gray-500 mt-1">Menunjukkan dominasi biaya berdasarkan kategori vendor.</p>
          </div>
          <div className="mt-6 w-full max-w-full">
            <OperationalCostDonutChart data={pieData} />
          </div>
        </article>

        <article className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 h-full overflow-hidden max-w-full">
          <div>
            <p className="text-sm font-semibold text-gray-500">Status Pembayaran Klien</p>
            <h2 className="text-lg font-bold text-gray-900 mt-1">Tagihan vs Pelunasan</h2>
            <p className="text-xs text-gray-500 mt-1">Total Tagihan, sudah dibayar, dan sisa pelunasan.</p>
          </div>
          <div className="mt-6 h-[320px] w-full max-w-full overflow-hidden">
            <ResponsiveContainer width="100%" height="100%" debounce={80}>
              <BarChart data={barData} margin={{ top: 10, right: 12, left: 0, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" stroke="#E5E7EB" />
                <XAxis dataKey="label" stroke="#475569" tickLine={false} axisLine={false} />
                <YAxis stroke="#475569" tickFormatter={(value) => `Rp${value / 1000}k`} tickLine={false} axisLine={false} />
                <Tooltip
                  formatter={tooltipFormatter}
                  wrapperStyle={{ zIndex: 9999 }}
                  contentStyle={{ borderRadius: 16, border: '1px solid #E5E7EB', zIndex: 9999 }}
                />
                <Bar dataKey="nominal" fill={chartColors.emerald[3]} radius={[12, 12, 0, 0]}>
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
