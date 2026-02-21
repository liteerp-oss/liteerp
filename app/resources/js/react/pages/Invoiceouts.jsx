import React from "react";
import DashboardLayout from "../layouts/DashboardLayout";
import IndexInvoiceOuts from "../components/Invoice/IndexInvoiceOuts";
import { useSearchParams } from "react-router-dom";
import InvoiceOutDetail from "../components/Invoice/InvoiceOuts/InvoiceOutsDetail";
import PageHead from "../components/PageHead";
import { useI18n } from "@/i18n/useI18n";
export default function InvoiceOuts() {
  const {t} = useI18n();
  const [searchParams] = useSearchParams();
  return (
    <DashboardLayout>
      {searchParams.get('form') && searchParams.get('id') ? <InvoiceOutDetail/> : <div>
        <PageHead
                      containerClass='mx-4'
                      title={t('Invoice outs')}
                      subtitle={t('invoice_out_desc')}
                      />
        <div className="m-4">

          <IndexInvoiceOuts />

        </div>
      </div> }
      
    </DashboardLayout>
  );
}
