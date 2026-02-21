import React from "react";
import DashboardLayout from "../layouts/DashboardLayout";
import IndexInvoiceIns from "../components/Invoice/IndexInvoiceIns";
import { useSearchParams } from "react-router-dom";
import InvoiceInDetail from "../components/Invoice/InvoiceIns/InvoiceInDetail";
import PageHead from "../components/PageHead";
import { useI18n } from "@/i18n/useI18n";
export default function Invoice() {
  const {t} = useI18n();
  const [searchParams] = useSearchParams();
  return (
    <DashboardLayout>
      {searchParams.get('form') && searchParams.get('id') ? <div>
        {searchParams.get('form') === 'invoicein' ? <InvoiceInDetail/> : null}
      </div> : <div>
        <PageHead
                      containerClass='mx-4'
                      title={t('Invoice ins')}
                      subtitle={t('invoice_ins_desc')}
                      />
        <div className="m-4">
          <IndexInvoiceIns />
        </div>
      </div> }
      
    </DashboardLayout>
  );
}
