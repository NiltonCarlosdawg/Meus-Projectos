import { BiPhone } from "react-icons/bi"
import { people } from "../object/object"
import { MdEmail } from "react-icons/md"
import { CiLocationOn } from "react-icons/ci"

const DashContact = () => {
  return (
    <div className="h-[608px] flex justify-center my-16 gap-8 px-[6%] max-[980px]:flex-col items-center">
      <div className="bg-white p-4 rotate-1 w-[40%] h-[300px] hover:rotate-3 transition text-[30px] text-center flex items-center justify-center flex-col max-[920px]:text-[20px] max-[920px]:w-[70%] max-[380px]:w-[100%] z-[1]">
        <BiPhone size={60}/>
        <div>
          +244 933419095
        </div>
      </div>
      <div className="bg-white p-4 rotate-1 w-[40%] h-[300px] hover:rotate-3 transition text-[30px] text-center flex items-center justify-center flex-col max-[920px]:text-[20px] max-[920px]:w-[70%] max-[380px]:w-[100%] z-[1]">
        <MdEmail size={60}/>
        <div>
          monsoir@gmail.com
        </div>
      </div>
      <div className="bg-white p-4 rotate-1 w-[40%] h-[300px] hover:rotate-3 transition text-[30px] text-center flex items-center justify-center flex-col max-[920px]:text-[20px] max-[920px]:w-[70%] max-[380px]:w-[100%] z-[1]">
        <CiLocationOn size={60}/>
        <div>
          Vila Alice ao lado do colegio Bem dizer
        </div>
      </div>
    </div>
  )
}

export default DashContact