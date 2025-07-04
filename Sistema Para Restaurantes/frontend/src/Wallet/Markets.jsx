import { useSelector } from "react-redux"
import Footer from "./footer/Footer"
import Nav from "./header/Nav"
import DashMark from "./section/Markets/DashMark"

const Markets = () => {
  const myvalue = useSelector(state=>state.boolean.boolean)
  
  return (
    <div className={myvalue ?"bg-[#fff] text-[black] py-[10px] transition-[1s]":"bg-[#181a20] text-[#fff]  py-[10px] transition-[1s]"}>
      <Nav/>
      <DashMark/>
      <Footer/>
    </div>
  )
}

export default Markets