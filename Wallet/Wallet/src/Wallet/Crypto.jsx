import { useSelector } from "react-redux"
import Footer from "./footer/Footer"
import Nav from "./header/Nav"
import Buy from "./section/Buy/Buy"


const Crypto = () => {
  const myvalue = useSelector(state=>state.boolean.boolean)
  return (
    <div className={myvalue ?"bg-[#fff] text-[black] py-[10px] transition-[1s]":"bg-[#181a20] text-[#fff]  py-[10px] transition-[1s]"}>
      <Nav/>
      <Buy/>

      <Footer/>
    </div>
  )
}

export default Crypto