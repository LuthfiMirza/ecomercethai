import React from 'react';

const CheckoutApp = ({ initialData }) => {
  return (
    <div className="rounded-3xl border border-orange-100 bg-white/80 p-6 text-center text-sm text-slate-500">
      React checkout placeholder. Data total item: {initialData?.items?.length || 0}
    </div>
  );
};

export default CheckoutApp;
