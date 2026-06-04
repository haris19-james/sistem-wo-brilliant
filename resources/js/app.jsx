import React from 'react';
import { createRoot } from 'react-dom/client';
import FinanceDashboardCharts from './components/FinanceDashboardCharts';

const rootElement = document.getElementById('admin-finance-charts-root');
if (rootElement) {
  const chartData = window.AdminFinanceChartsData || {
    monthlyRevenue: [],
    vendorExpenses: [],
    paymentStatus: [],
  };

  createRoot(rootElement).render(
    <React.StrictMode>
      <FinanceDashboardCharts {...chartData} />
    </React.StrictMode>
  );
}
