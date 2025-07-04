import { useSelector } from "react-redux"
import Footer from "./footer/Footer"
import Nav from "./header/Nav"
import DashTrade from "./section/Trade/DashTrade"
import { useState } from "react"

const Trade = () => {
  const [darkmode, setDarkmode] = useState(false)
  const myvalue = useSelector(state=>state.boolean.boolean)
  
  return (
    <div className={myvalue ?"bg-[#fff] text-[black] py-[10px] transition-[1s]":"bg-[#181a20] text-[#fff]  py-[10px] transition-[1s]"}>
      <Nav darkmode={darkmode}/>
      <DashTrade/>
      <Footer/>
    </div>
  )
}

export default Trade