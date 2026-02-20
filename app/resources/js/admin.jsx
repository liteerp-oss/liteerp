import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";

// Pages
import Login from "./react/pages/login";
import Dashboard from "./react/pages/dashboard";
import Product from "./react/pages/product";
import Order from "./react/pages/order";
import Customer from "./react/pages/customer";
import Warehouse from "./react/pages/warehouse";
import InvoiceIns from "./react/pages/invoiceins";
import Setting from "./react/pages/setting";
import User from "./react/pages/user";
import Register from "./react/pages/register";
import Business from "./react/pages/business";
import VerifyAccount from "./react/pages/verify-account";
import Notification from "./react/pages/notification";
import StockIns from "./react/pages/stockin";
import Purchases from "./react/pages/purchases";
import Suppliers from "./react/pages/suppliers";
import Shipping from "./react/pages/shipping";
import Inventory from "./react/pages/Inventory";
import ActivityLogs from "./react/pages/activity-logs";
import Profile from "./react/pages/Profile";
import ForgetPassword from "./react/pages/forget-password";
import ResetPassword from "./react/pages/reset-password";
import Logout from "./react/pages/Logout";
import Extensions from "./react/pages/extensions";
import Wrapper from "@/react/wrappers/Wrapper";
import routeRegistry from '@core/RouteRegistry'
import Permissions from "./react/pages/permissions";
import InvoiceOuts from "./react/pages/Invoiceouts";
import CustomInvoiceIns from "./react/pages/custominvoiceins";
import CustomInvoiceOuts from "./react/pages/custominvoiceouts";
import CustomerGroup from "./react/pages/customergroup";
import CategoryProduct from "./react/pages/categoryproduct";
import Pricelist from "./react/pages/pricelist";
import StockOuts from "./react/pages/stockout";
import InventoryAdjustments from "./react/pages/inventoryadjustments";
const App = () => {
  const routes = routeRegistry.all();
  return (
    <Wrapper>
      <BrowserRouter basename="/dashboard">
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/verify-account" element={<VerifyAccount />} />
          <Route path="/products" element={<Product />} />
          <Route path="/category-product" element={<CategoryProduct />} />
          <Route path="/price-list" element={<Pricelist />} />
          <Route path="/orders" element={<Order />} />
          <Route path="/customers" element={<Customer />} />
          <Route path="/customer-groups" element={<CustomerGroup />} />
          <Route path="/warehouses" element={<Warehouse />} />
          <Route path="/invoice-ins" element={<InvoiceIns />} />
          <Route path="/invoice-outs" element={<InvoiceOuts/>}/>
          <Route path="/custom-invoice-ins" element={<CustomInvoiceIns />} />
          <Route path="/custom-invoice-outs" element={<CustomInvoiceOuts/>}/>
          <Route path="/settings" element={<Setting />} />
          <Route path="/users" element={<User />} />
          <Route path="/business" element={<Business />} />
          <Route path="/notification" element={<Notification />} />
          <Route path="/stock-ins" element={<StockIns />} />
          <Route path="/stock-outs" element={<StockOuts />} />
          <Route path="/inventories" element={<Inventory />} />
          <Route path="/inventories-adjustments" element={<InventoryAdjustments />} />
          <Route path="/purchases" element={<Purchases />} />
          <Route path="/suppliers" element={<Suppliers />} />
          <Route path="/shippings" element={<Shipping />} />
          <Route path="/activity-logs" element={<ActivityLogs />} />
          <Route path="/profile" element={<Profile />} />
          <Route path="/forget-password" element={<ForgetPassword />} />
          <Route path="/reset-password" element={<ResetPassword />} />
          <Route path="/logout" element={<Logout />} />
          <Route path="/extensions" element={<Extensions />} />
          <Route path="/permission-group" element={<Permissions />} />
          {routes.map(r => (
            <Route key={r.path} path={r.path} element={<r.component />} />
          ))}
        </Routes>
      </BrowserRouter>
    </Wrapper>
  );
};

if (document.getElementById("app")) {
  ReactDOM.createRoot(document.getElementById("app")).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
}
