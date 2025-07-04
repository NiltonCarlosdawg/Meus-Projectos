import Home from "./Wallet/Home"
import "./App.css"
import Trade from "./Wallet/Trade"
import { BrowserRouter, Route, Routes } from "react-router-dom"
import Crypto from "./Wallet/Crypto"
import Admin from "./Wallet/Admin"
import Markets from "./Wallet/Markets"
import Vip from "./Wallet/Vip"
function App() {
  return (
    <>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Home/>}/>
          <Route path="/trade" element={<Trade/>}/>
          <Route path="/buy" element={<Crypto/>}/>
          <Route path="/admin" element={<Admin/>}/>
          <Route path="/markets" element={<Markets/>}/>
          <Route path="/vip" element={<Vip/>}/>
        </Routes>
      </BrowserRouter>
    </>
  )
}

export default App
