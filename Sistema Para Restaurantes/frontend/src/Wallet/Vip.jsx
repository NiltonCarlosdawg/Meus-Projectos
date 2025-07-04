import { useSelector } from "react-redux"
import Footer from "./footer/Footer"
import Nav from "./header/Nav"
import DashVip from "./section/Vip/DashVip"

const Vip = () => {
  const myvalue = useSelector(state=>state.boolean.boolean)
  return (
    <div className={myvalue ?"bg-[#fff] text-[black] py-[10px] transition-[1s]":"bg-[#181a20] text-[#fff]  py-[10px] transition-[1s]"}>
      <Nav/>
      <DashVip/>
      <Footer/>
    </div>
  )
}

export default Vip